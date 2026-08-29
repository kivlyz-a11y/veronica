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
        $slots = $this->scheduleModel->getAvailableSlots($dateFilter);
        $officers = $this->userModel->where('status', 'active')->whereIn('role', ['officer', 'admin'])->findAll();
        $holidays = $this->holidayModel->orderBy('holiday_date', 'ASC')->findAll();

        $data = [
            'title'      => 'Manajemen Jadwal & Slot Pelayanan - SI VERONIKA',
            'dateFilter' => $dateFilter,
            'slots'      => $slots,
            'officers'   => $officers,
            'holidays'   => $holidays,
        ];

        return view('admin/schedules/index', $data);
    }

    public function store()
    {
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
