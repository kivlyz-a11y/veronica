<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\HolidayModel;
use App\Models\ScheduleModel;
use App\Models\ServiceSubcategoryModel;

class ScheduleApiController extends BaseController
{
    protected ScheduleModel $scheduleModel;
    protected HolidayModel $holidayModel;
    protected ServiceSubcategoryModel $subcatModel;

    public function __construct()
    {
        $this->scheduleModel = new ScheduleModel();
        $this->holidayModel  = new HolidayModel();
        $this->subcatModel   = new ServiceSubcategoryModel();
    }

    /**
     * Get available slots for a given date
     * GET /api/schedules/slots?date=YYYY-MM-DD
     */
    public function getSlots()
    {
        $date = $this->request->getGet('date');
        if (empty($date) || !strtotime($date)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tanggal tidak valid.',
                'slots'   => [],
            ]);
        }

        // 1. Check if date is in the past
        $today = date('Y-m-d');
        if ($date < $today) {
            return $this->response->setJSON([
                'success'    => false,
                'is_holiday' => false,
                'message'    => 'Tidak dapat memilih tanggal yang sudah terlewat.',
                'slots'      => [],
            ]);
        }

        // 2. Check if weekend (Saturday = 6, Sunday = 7)
        $dayOfWeek = (int)date('N', strtotime($date));
        if ($dayOfWeek >= 6) {
            return $this->response->setJSON([
                'success'    => false,
                'is_holiday' => true,
                'message'    => 'Pelayanan Pengadilan Agama Penajam tutup pada hari Sabtu dan Minggu (Hari Libur Akhir Pekan).',
                'slots'      => [],
            ]);
        }

        // 3. Check official holidays
        $holiday = $this->holidayModel->where('holiday_date', $date)->where('status', 'active')->first();
        if ($holiday) {
            return $this->response->setJSON([
                'success'      => false,
                'is_holiday'   => true,
                'holiday_name' => $holiday['name'],
                'message'      => "Tanggal {$date} adalah hari libur nasional ({$holiday['name']}). Layanan tidak tersedia.",
                'slots'        => [],
            ]);
        }

        // 4. Fetch schedule slots
        $slots = $this->scheduleModel->getAvailableSlots($date);

        if (empty($slots)) {
            return $this->response->setJSON([
                'success'    => true,
                'is_holiday' => false,
                'message'    => 'Belum tersedia jadwal pelayanan pada tanggal ini.',
                'slots'      => [],
            ]);
        }

        return $this->response->setJSON([
            'success'    => true,
            'is_holiday' => false,
            'message'    => 'Slot jadwal berhasil dimuat.',
            'slots'      => $slots,
        ]);
    }

    /**
     * Get subcategories for a service
     * GET /api/services/{id}/subcategories
     */
    public function getSubcategories(int $serviceId)
    {
        $subcategories = $this->subcatModel->getByServiceId($serviceId);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $subcategories,
        ]);
    }
}
