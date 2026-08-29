<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ApplicationModel;
use App\Models\ScheduleModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected ApplicationModel $applicationModel;
    protected ScheduleModel $scheduleModel;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->scheduleModel    = new ScheduleModel();
    }

    public function index()
    {
        helper('veronika');
        $db = \Config\Database::connect();

        $today = date('Y-m-d');

        // Aggregated Stats
        $stats = [
            'total'               => $db->table('applications')->countAllResults(),
            'menunggu_verifikasi' => $db->table('applications')->where('status', 'Menunggu Verifikasi')->countAllResults(),
            'disetujui'           => $db->table('applications')->whereIn('status', ['Disetujui', 'Terjadwal'])->countAllResults(),
            'sedang_berlangsung'  => $db->table('applications')->where('status', 'Sedang Berlangsung')->countAllResults(),
            'selesai'             => $db->table('applications')->where('status', 'Selesai')->countAllResults(),
            'dibatalkan'          => $db->table('applications')->where('status', 'Dibatalkan')->countAllResults(),
            'tidak_hadir'         => $db->table('applications')->where('status', 'Tidak Hadir')->countAllResults(),
            'hari_ini'            => $db->table('applications')
                                        ->join('appointments', 'appointments.application_id = applications.id')
                                        ->join('schedules', 'schedules.id = appointments.schedule_id')
                                        ->where('schedules.date', $today)
                                        ->countAllResults(),
        ];

        // Today's schedule table
        $todaySchedules = $db->table('applications')
            ->select('
                applications.id,
                applications.registration_number,
                applications.status,
                applicants.name as applicant_name,
                applicants.phone as applicant_phone,
                services.name as service_name,
                schedules.start_time,
                schedules.end_time,
                appointments.zoom_url,
                appointments.check_in_at
            ')
            ->join('applicants', 'applicants.id = applications.applicant_id')
            ->join('services', 'services.id = applications.service_id')
            ->join('appointments', 'appointments.application_id = applications.id')
            ->join('schedules', 'schedules.id = appointments.schedule_id')
            ->where('schedules.date', $today)
            ->orderBy('schedules.start_time', 'ASC')
            ->get()
            ->getResultArray();

        // Chart Data: Applications last 7 days
        $last7Days = [];
        $chartDates = [];
        $chartCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $chartDates[] = date('d M', strtotime($d));
            $cnt = $db->table('applications')->where('DATE(created_at)', $d)->countAllResults();
            $chartCounts[] = $cnt;
        }

        // Service Distribution Chart
        $serviceDist = $db->table('services')
            ->select('services.name, count(applications.id) as total')
            ->join('applications', 'applications.service_id = services.id', 'left')
            ->groupBy('services.id')
            ->get()
            ->getResultArray();

        $data = [
            'title'          => 'Dashboard Utama - SI VERONIKA',
            'stats'          => $stats,
            'todaySchedules' => $todaySchedules,
            'chartDates'     => json_encode($chartDates),
            'chartCounts'    => json_encode($chartCounts),
            'serviceDist'    => $serviceDist,
        ];

        return view('admin/dashboard', $data);
    }
}
