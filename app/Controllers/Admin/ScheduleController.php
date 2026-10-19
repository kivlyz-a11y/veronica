<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HolidayModel;
use App\Models\ScheduleModel;
use App\Models\UserModel;
use App\Services\Audit\AuditService;

class ScheduleController extends BaseController
{
    protected ScheduleModel $scheduleModel;
    protected HolidayModel $holidayModel;
    protected UserModel $userModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->scheduleModel = new ScheduleModel();
        $this->holidayModel  = new HolidayModel();
        $this->userModel     = new UserModel();
        $this->auditService  = new AuditService();
    }

    public function index()
    {
        helper(['form', 'veronika']);

        $dateFilter = $this->request->getGet('date') ?? date('Y-m-d');
        $slots = $this->scheduleModel->getAvailableSlots($dateFilter, false);
        $officers = $this->userModel->where('status', 'active')->whereIn('role', ['officer', 'admin'])->findAll();
        $holidays = $this->holidayModel->orderBy('holiday_date', 'ASC')->findAll();

        $prevDate = date('Y-m-d', strtotime($dateFilter . ' -1 day'));
        $nextDate = date('Y-m-d', strtotime($dateFilter . ' +1 day'));

        // Query upcoming dates with slots count
        $upcomingDates = $this->scheduleModel
            ->select('date, count(*) as slot_count, sum(booked) as total_booked')
            ->where('date >=', date('Y-m-d'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->findAll(60);

        // Find nearest date with slots if current selected date has no slots
        $nearestDate = null;
        if (empty($slots)) {
            $nearest = $this->scheduleModel
                ->select('date, count(*) as slot_count')
                ->where('date >=', $dateFilter)
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->first();
            if ($nearest) {
                $nearestDate = $nearest;
            }
        }

        $data = [
            'title'         => 'Manajemen Jadwal & Slot Pelayanan - SI VERONICA',
            'dateFilter'    => $dateFilter,
            'prevDate'      => $prevDate,
            'nextDate'      => $nextDate,
            'slots'         => $slots,
            'officers'      => $officers,
            'holidays'      => $holidays,
            'upcomingDates' => $upcomingDates,
            'nearestDate'   => $nearestDate,
        ];

        return view('admin/schedules/index', $data);
    }

    public function store()
    {
        if ($this->request->getPost('repeat_mode') === 'recurring') {
            return $this->storeRecurring();
        }

        $rules = [
            'date'       => 'required|valid_date',
            'start_time' => 'required',
            'end_time'   => 'required',
            'capacity'   => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startTime = $this->request->getPost('start_time');
        $endTime   = $this->request->getPost('end_time');

        if ($endTime <= $startTime) {
            return redirect()->back()->withInput()->with('error', 'Jam selesai harus lebih besar dari jam mulai.');
        }

        $date = $this->request->getPost('date');
        $data = [
            'date'                => $date,
            'start_time'          => $startTime,
            'end_time'            => $endTime,
            'capacity'            => (int)$this->request->getPost('capacity'),
            'assigned_officer_id' => $this->request->getPost('assigned_officer_id') ?: null,
            'notes'               => $this->request->getPost('notes'),
            'status'              => 'active',
        ];

        $this->scheduleModel->insert($data);

        $this->auditService->log(
            session()->get('user_id'),
            'create_schedule',
            'schedules',
            $date,
            "Menambahkan slot jadwal baru pada {$date} ({$startTime} - {$endTime})."
        );

        return redirect()->to(site_url("admin/schedules?date={$date}"))->with('message', 'Slot jadwal berhasil ditambahkan.');
    }

    /**
     * Store recurring schedules (Senin s.d. Jumat atau hari kerja kustom)
     */
    public function storeRecurring()
    {
        $startDateStr = $this->request->getPost('start_date');
        $endDateStr   = $this->request->getPost('end_date');

        if (empty($startDateStr) || empty($endDateStr)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal mulai dan tanggal selesai wajib diisi.');
        }

        $startDate = new \DateTime($startDateStr);
        $endDate   = new \DateTime($endDateStr);

        if ($endDate < $startDate) {
            return redirect()->back()->withInput()->with('error', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        }

        $interval = $startDate->diff($endDate);
        if ($interval->days > 180) {
            return redirect()->back()->withInput()->with('error', 'Rentang tanggal maksimal pembuatan jadwal berulang adalah 180 hari (6 bulan).');
        }

        $selectedDays = $this->request->getPost('days');
        if (empty($selectedDays) || !is_array($selectedDays)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu hari berulang dalam seminggu.');
        }
        $selectedDays = array_map('intval', $selectedDays);

        $slotMode = $this->request->getPost('slot_mode') ?? 'preset';
        $slotsToGenerate = [];

        if ($slotMode === 'custom') {
            $st = $this->request->getPost('start_time');
            $et = $this->request->getPost('end_time');
            if (empty($st) || empty($et) || $et <= $st) {
                return redirect()->back()->withInput()->with('error', 'Jam selesai harus lebih besar dari jam mulai.');
            }
            $slotsToGenerate[] = [
                'start' => strlen($st) === 5 ? $st . ':00' : $st,
                'end'   => strlen($et) === 5 ? $et . ':00' : $et,
            ];
        } else {
            $presetSlots = $this->request->getPost('preset_slots');
            if (empty($presetSlots) || !is_array($presetSlots)) {
                return redirect()->back()->withInput()->with('error', 'Pilih minimal satu pilihan jam slot.');
            }
            foreach ($presetSlots as $p) {
                $parts = explode('-', $p);
                if (count($parts) === 2) {
                    $s = trim($parts[0]);
                    $e = trim($parts[1]);
                    $slotsToGenerate[] = [
                        'start' => strlen($s) === 5 ? $s . ':00' : $s,
                        'end'   => strlen($e) === 5 ? $e . ':00' : $e,
                    ];
                }
            }
        }

        if (empty($slotsToGenerate)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada slot jam yang valid untuk digenerate.');
        }

        $skipHolidays = (bool)$this->request->getPost('skip_holidays');
        $holidayDates = [];
        if ($skipHolidays) {
            $holidays = $this->holidayModel->where('status', 'active')->findAll();
            foreach ($holidays as $h) {
                $holidayDates[$h['holiday_date']] = $h['name'];
            }
        }

        $skipExisting        = (bool)$this->request->getPost('skip_existing');
        $skipFridayAfternoon = (bool)$this->request->getPost('skip_friday_afternoon');
        $capacity            = max(1, (int)$this->request->getPost('capacity'));
        $assignedOfficerId   = $this->request->getPost('assigned_officer_id') ?: null;
        $notes               = trim((string)$this->request->getPost('notes')) ?: 'Slot Rutin PTSP Online';

        $batch                = [];
        $skippedExistingCount = 0;
        $skippedHolidayCount  = 0;
        $processedDatesCount  = 0;

        $curr = clone $startDate;
        while ($curr <= $endDate) {
            $dayOfWeek = (int)$curr->format('N'); // 1 = Senin, 7 = Minggu
            $dateStr   = $curr->format('Y-m-d');

            if (in_array($dayOfWeek, $selectedDays, true)) {
                if ($skipHolidays && isset($holidayDates[$dateStr])) {
                    $skippedHolidayCount++;
                    $curr->modify('+1 day');
                    continue;
                }

                $processedDatesCount++;

                $existingStarts = [];
                if ($skipExisting) {
                    $existingList = $this->scheduleModel->where('date', $dateStr)->select('start_time')->findAll();
                    foreach ($existingList as $ex) {
                        $existingStarts[$ex['start_time']] = true;
                    }
                }

                foreach ($slotsToGenerate as $sl) {
                    // Jika hari Jumat dan opsi lewati sore aktif (ibadah sholat Jumat & jam pulang kantor)
                    if ($dayOfWeek === 5 && $skipFridayAfternoon && $sl['start'] >= '11:30:00') {
                        continue;
                    }

                    if ($skipExisting && isset($existingStarts[$sl['start']])) {
                        $skippedExistingCount++;
                        continue;
                    }

                    $batch[] = [
                        'date'                => $dateStr,
                        'start_time'          => $sl['start'],
                        'end_time'            => $sl['end'],
                        'capacity'            => $capacity,
                        'booked'              => 0,
                        'assigned_officer_id' => $assignedOfficerId,
                        'status'              => 'active',
                        'notes'               => $notes,
                        'created_at'          => date('Y-m-d H:i:s'),
                        'updated_at'          => date('Y-m-d H:i:s'),
                    ];
                }
            }

            $curr->modify('+1 day');
        }

        if (empty($batch)) {
            $msg = 'Tidak ada slot baru yang ditambahkan. ';
            if ($skippedExistingCount > 0) {
                $msg .= "{$skippedExistingCount} slot sudah ada sebelumnya. ";
            }
            if ($skippedHolidayCount > 0) {
                $msg .= "{$skippedHolidayCount} hari libur dilewati.";
            }
            $targetDate = !empty($existingSlots) ? $existingSlots[0]['date'] : $startDateStr;
            return redirect()->to(site_url("admin/schedules?date={$targetDate}"))->with('message', $msg);
        }

        $this->scheduleModel->insertBatch($batch);

        $totalInserted   = count($batch);
        $firstActiveDate = $batch[0]['date'];
        $lastActiveDate  = $batch[count($batch) - 1]['date'];

        $this->auditService->log(
            session()->get('user_id'),
            'create_recurring_schedule',
            'schedules',
            "{$startDateStr} s/d {$endDateStr}",
            "Membuat {$totalInserted} slot jadwal berulang dari {$startDateStr} s.d. {$endDateStr}."
        );

        $successMsg = "Berhasil membuat {$totalInserted} slot jadwal pada {$processedDatesCount} hari kerja ({$firstActiveDate} s/d {$lastActiveDate}).";
        if ($skippedHolidayCount > 0) {
            $successMsg .= " ({$skippedHolidayCount} hari libur nasional dilewati)";
        }
        if ($skippedExistingCount > 0) {
            $successMsg .= " ({$skippedExistingCount} slot yang sudah ada sebelumnya dilewati)";
        }

        return redirect()->to(site_url("admin/schedules?date={$firstActiveDate}"))->with('message', $successMsg);
    }

    public function toggleStatus(int $id)
    {
        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $newStatus = ($schedule['status'] === 'active') ? 'closed' : 'active';
        $this->scheduleModel->update($id, ['status' => $newStatus]);

        $this->auditService->log(
            session()->get('user_id'),
            'toggle_schedule',
            'schedules',
            (string)$id,
            "Mengubah status slot #{$id} ({$schedule['date']} {$schedule['start_time']}) menjadi {$newStatus}."
        );

        return redirect()->back()->with('message', "Status slot berhasil diubah menjadi {$newStatus}.");
    }

    public function delete(int $id)
    {
        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        if ($schedule['booked'] > 0) {
            return redirect()->back()->with('error', 'Jadwal yang sudah memiliki permohonan booking tidak dapat dihapus.');
        }

        $this->scheduleModel->delete($id);

        $this->auditService->log(
            session()->get('user_id'),
            'delete_schedule',
            'schedules',
            (string)$id,
            "Menghapus slot jadwal #{$id} ({$schedule['date']} {$schedule['start_time']})."
        );

        return redirect()->back()->with('message', 'Slot jadwal berhasil dihapus.');
    }

    public function storeHoliday()
    {
        $rules = [
            'holiday_date' => 'required|valid_date|is_unique[holidays.holiday_date]',
            'name'         => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal libur tidak valid atau sudah terdaftar.');
        }

        $this->holidayModel->insert([
            'holiday_date' => $this->request->getPost('holiday_date'),
            'name'         => $this->request->getPost('name'),
            'status'       => 'active',
        ]);

        return redirect()->back()->with('message', 'Hari libur berhasil ditambahkan.');
    }

    public function deleteHoliday(int $id)
    {
        $this->holidayModel->delete($id);
        return redirect()->back()->with('message', 'Hari libur berhasil dihapus.');
    }
}
