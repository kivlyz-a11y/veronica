<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\ApplicationModel;
use App\Models\AppointmentModel;
use App\Services\Audit\AuditService;

class CheckInController extends BaseController
{
    protected ApplicationModel $applicationModel;
    protected AppointmentModel $appointmentModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->appointmentModel = new AppointmentModel();
        $this->auditService     = new AuditService();
    }

    public function index()
    {
        helper(['form', 'veronika']);

        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $todayQueue = $db->table('applications')
            ->select('
                applications.*,
                applicants.name as applicant_name,
                applicants.phone as applicant_phone,
                services.name as service_name,
                schedules.start_time,
                schedules.end_time,
                appointments.id as appointment_id,
                appointments.zoom_url,
                appointments.check_in_at,
                appointments.service_started_at,
                appointments.service_ended_at
            ')
            ->join('applicants', 'applicants.id = applications.applicant_id')
            ->join('services', 'services.id = applications.service_id')
            ->join('appointments', 'appointments.application_id = applications.id')
            ->join('schedules', 'schedules.id = appointments.schedule_id')
            ->where('schedules.date', $today)
            ->whereNotIn('applications.status', ['Dibatalkan', 'Ditolak'])
            ->orderBy('schedules.start_time', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'      => 'Pelayanan & Check-In Online - SI VERONIKA',
            'todayQueue' => $todayQueue,
        ];

        return view('officer/checkin', $data);
    }

    /**
     * Check-in action
     */
    public function checkIn(int $applicationId)
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }

        $userId = session()->get('user_id');

        $this->appointmentModel->where('application_id', $applicationId)->set([
            'check_in_at' => date('Y-m-d H:i:s'),
            'check_in_by' => $userId,
            'status'      => 'checked_in',
        ])->update();

        $this->applicationModel->update($applicationId, [
            'status' => 'Sedang Diverifikasi',
        ]);

        $this->auditService->log(
            $userId,
            'check_in',
            'applications',
            $app['registration_number'],
            "Petugas melakukan Check-In untuk pemohon {$app['applicant_name']}."
        );

        return redirect()->back()->with('message', "Pemohon #{$app['registration_number']} berhasil Check-In.");
    }

    /**
     * Start Service (Mulai Pelayanan Zoom)
     */
    public function startService(int $applicationId)
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }

        $userId = session()->get('user_id');

        $this->appointmentModel->where('application_id', $applicationId)->set([
            'service_started_at' => date('Y-m-d H:i:s'),
            'status'             => 'in_service',
        ])->update();

        $this->applicationModel->update($applicationId, [
            'status' => 'Sedang Berlangsung',
        ]);

        $this->auditService->log(
            $userId,
            'start_service',
            'applications',
            $app['registration_number'],
            "Memulai sesi pelayanan online Zoom dengan {$app['applicant_name']}."
        );

        return redirect()->back()->with('message', "Sesi pelayanan dimulai untuk #{$app['registration_number']}.");
    }

    /**
     * Finish Service (Selesaikan Pelayanan)
     */
    public function finishService(int $applicationId)
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }

        $userId = session()->get('user_id');

        $this->appointmentModel->where('application_id', $applicationId)->set([
            'service_ended_at' => date('Y-m-d H:i:s'),
            'status'           => 'completed',
        ])->update();

        $this->applicationModel->update($applicationId, [
            'status' => 'Selesai',
        ]);

        $this->auditService->log(
            $userId,
            'finish_service',
            'applications',
            $app['registration_number'],
            "Menyelesaikan sesi pelayanan untuk #{$app['registration_number']} ({$app['applicant_name']})."
        );

        return redirect()->back()->with('message', "Pelayanan untuk #{$app['registration_number']} telah SELESAI.");
    }

    /**
     * Mark Absent (Tandai Tidak Hadir)
     */
    public function markAbsent(int $applicationId)
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }

        $userId = session()->get('user_id');

        $this->appointmentModel->where('application_id', $applicationId)->set([
            'status' => 'absent',
        ])->update();

        $this->applicationModel->update($applicationId, [
            'status' => 'Tidak Hadir',
        ]);

        $this->auditService->log(
            $userId,
            'mark_absent',
            'applications',
            $app['registration_number'],
            "Menandai pemohon #{$app['registration_number']} ({$app['applicant_name']}) Tidak Hadir pada jadwal Zoom."
        );

        return redirect()->back()->with('message', "Status pemohon #{$app['registration_number']} diubah menjadi Tidak Hadir.");
    }
}
