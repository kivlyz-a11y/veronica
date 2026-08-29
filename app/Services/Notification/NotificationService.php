<?php

namespace App\Services\Notification;

use App\Models\ApplicationModel;
use App\Models\NotificationModel;
use App\Models\SystemSettingModel;
use App\Services\WhatsApp\WhatsAppService;

class NotificationService
{
    protected ApplicationModel $applicationModel;
    protected NotificationModel $notificationModel;
    protected SystemSettingModel $settingModel;
    protected WhatsAppService $waService;

    public function __construct()
    {
        $this->applicationModel  = new ApplicationModel();
        $this->notificationModel = new NotificationModel();
        $this->settingModel      = new SystemSettingModel();
        $this->waService         = new WhatsAppService();
    }

    /**
     * Send initial registration confirmation via WhatsApp
     */
    public function sendRegistrationConfirmation(int $applicationId): array
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app || empty($app['applicant_phone'])) {
            return ['success' => false, 'message' => 'Data pemohon atau nomor WhatsApp tidak valid.'];
        }

        $template = $this->settingModel->getVal(
            'wa_template_registration',
            "Pendaftaran SI VERONIKA Pengadilan Agama Penajam berhasil.\n\nNomor Registrasi: {{nomor_registrasi}}\nNama: {{nama}}\nLayanan: {{layanan}}\nTanggal: {{tanggal}}\nWaktu: {{waktu}} WITA\n\nStatus permohonan Anda: Menunggu Verifikasi.\n\nMohon menunggu informasi selanjutnya.\n\nPengadilan Agama Penajam."
        );

        $dateFormatted = date('d F Y', strtotime($app['schedule_date']));
        $timeFormatted = substr($app['schedule_start_time'], 0, 5) . '–' . substr($app['schedule_end_time'], 0, 5);

        $message = $this->waService->renderTemplate($template, [
            'nomor_registrasi' => $app['registration_number'],
            'nama'             => $app['applicant_name'],
            'layanan'          => $app['service_name'] . ($app['sub_service_name'] ? ' - ' . $app['sub_service_name'] : ''),
            'tanggal'          => $dateFormatted,
            'waktu'            => $timeFormatted,
            'status'           => $app['status'],
        ]);

        $eventKey = "app_{$app['id']}_registered";

        return $this->waService->send(
            $app['applicant_phone'],
            $message,
            'wa_confirmation',
            $app['id'],
            $eventKey
        );
    }

    /**
     * Send status update notification (e.g. Disetujui, Perlu Perbaikan, Ditolak)
     */
    public function sendStatusUpdateNotification(int $applicationId): array
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app || empty($app['applicant_phone'])) {
            return ['success' => false, 'message' => 'Data pemohon atau nomor WhatsApp tidak valid.'];
        }

        $template = $this->settingModel->getVal(
            'wa_template_status_update',
            "Pemberitahuan Status SI VERONIKA Pengadilan Agama Penajam:\n\nNomor Registrasi: {{nomor_registrasi}}\nNama: {{nama}}\nStatus: {{status}}\nCatatan Petugas: {{catatan}}\n\nSilakan cek status Anda melalui tautan:\n{{url_cek_status}}\n\nPengadilan Agama Penajam."
        );

        $message = $this->waService->renderTemplate($template, [
            'nomor_registrasi' => $app['registration_number'],
            'nama'             => $app['applicant_name'],
            'status'           => $app['status'],
            'catatan'          => !empty($app['verification_notes']) ? $app['verification_notes'] : '-',
            'layanan'          => $app['service_name'],
        ]);

        $statusSlug = url_title(strtolower($app['status']), '_');
        $eventKey = "app_{$app['id']}_status_{$statusSlug}_" . time();

        return $this->waService->send(
            $app['applicant_phone'],
            $message,
            'wa_status',
            $app['id'],
            $eventKey
        );
    }

    /**
     * Send Zoom link notification (when Zoom link is added/updated and nearing schedule)
     */
    public function sendZoomLinkNotification(int $applicationId, bool $force = false): array
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app || empty($app['applicant_phone'])) {
            return ['success' => false, 'message' => 'Data pemohon tidak ditemukan.'];
        }

        if (empty($app['zoom_url'])) {
            return ['success' => false, 'message' => 'Link Zoom belum tersedia. Silakan masukkan link Zoom sebelum mengirimkan notifikasi kepada pemohon.'];
        }

        if (in_array($app['status'], ['Dibatalkan', 'Tidak Hadir', 'Ditolak'])) {
            return ['success' => false, 'message' => 'Permohonan berstatus dibatalkan/ditolak/tidak hadir. Link Zoom tidak dikirimkan.'];
        }

        $template = $this->settingModel->getVal(
            'wa_template_zoom_link',
            "SI VERONIKA Pengadilan Agama Penajam\n\nLayanan Anda akan segera dimulai.\n\nNomor Registrasi: {{nomor_registrasi}}\nLayanan: {{layanan}}\nTanggal: {{tanggal}}\nWaktu: {{waktu}} WITA\n\nSilakan bergabung melalui Zoom:\n\n{{link_zoom}}\n\nMeeting ID: {{zoom_meeting_id}}\nPasscode: {{zoom_password}}\n\nMohon bergabung beberapa menit sebelum jadwal pelayanan.\n\nPengadilan Agama Penajam."
        );

        $dateFormatted = date('d F Y', strtotime($app['schedule_date']));
        $timeFormatted = substr($app['schedule_start_time'], 0, 5) . '–' . substr($app['schedule_end_time'], 0, 5);

        $message = $this->waService->renderTemplate($template, [
            'nomor_registrasi' => $app['registration_number'],
            'nama'             => $app['applicant_name'],
            'layanan'          => $app['service_name'],
            'tanggal'          => $dateFormatted,
            'waktu'            => $timeFormatted,
            'link_zoom'        => $app['zoom_url'],
            'zoom_meeting_id'  => $app['zoom_meeting_id'] ?? '-',
            'zoom_password'    => $app['zoom_password'] ?? '-',
        ]);

        $eventKey = $force ? "app_{$app['id']}_zoom_link_manual_" . time() : "app_{$app['id']}_zoom_link";

        return $this->waService->send(
            $app['applicant_phone'],
            $message,
            'wa_zoom_link',
            $app['id'],
            $eventKey
        );
    }

    /**
     * Send H-1 Reminder
     */
    public function sendH1Reminder(int $applicationId): array
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app || empty($app['applicant_phone'])) {
            return ['success' => false, 'message' => 'Data tidak ditemukan.'];
        }

        $template = $this->settingModel->getVal(
            'wa_template_reminder_h1',
            "Pengingat dari SI VERONIKA Pengadilan Agama Penajam:\n\nHalo Bapak/Ibu {{nama}}, Anda memiliki jadwal layanan online besok pada {{tanggal}} pukul {{waktu}} WITA.\nLayanan: {{layanan}}\nNomor Registrasi: {{nomor_registrasi}}\n\nMohon persiapkan dokumen administrasi Anda.\n\nPengadilan Agama Penajam."
        );

        $dateFormatted = date('d F Y', strtotime($app['schedule_date']));
        $timeFormatted = substr($app['schedule_start_time'], 0, 5) . '–' . substr($app['schedule_end_time'], 0, 5);

        $message = $this->waService->renderTemplate($template, [
            'nomor_registrasi' => $app['registration_number'],
            'nama'             => $app['applicant_name'],
            'layanan'          => $app['service_name'],
            'tanggal'          => $dateFormatted,
            'waktu'            => $timeFormatted,
        ]);

        $eventKey = "app_{$app['id']}_reminder_h1";

        return $this->waService->send(
            $app['applicant_phone'],
            $message,
            'wa_reminder_h1',
            $app['id'],
            $eventKey
        );
    }

    /**
     * Send H-1 Hour Reminder
     */
    public function sendH1hReminder(int $applicationId): array
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app || empty($app['applicant_phone'])) {
            return ['success' => false, 'message' => 'Data tidak ditemukan.'];
        }

        $template = $this->settingModel->getVal(
            'wa_template_reminder_h1h',
            "Pengingat dari SI VERONIKA Pengadilan Agama Penajam:\n\nHalo Bapak/Ibu {{nama}}, jadwal layanan Anda akan dimulai 1 jam lagi (pukul {{waktu}} WITA).\nNomor Registrasi: {{nomor_registrasi}}\n\nLink Zoom akan dikirimkan menjelang jadwal pelayanan.\n\nPengadilan Agama Penajam."
        );

        $timeFormatted = substr($app['schedule_start_time'], 0, 5) . '–' . substr($app['schedule_end_time'], 0, 5);

        $message = $this->waService->renderTemplate($template, [
            'nomor_registrasi' => $app['registration_number'],
            'nama'             => $app['applicant_name'],
            'layanan'          => $app['service_name'],
            'waktu'            => $timeFormatted,
        ]);

        $eventKey = "app_{$app['id']}_reminder_h1h";

        return $this->waService->send(
            $app['applicant_phone'],
            $message,
            'wa_reminder_h1h',
            $app['id'],
            $eventKey
        );
    }
}
