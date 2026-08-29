<?php

namespace App\Commands;

use App\Models\ApplicationModel;
use App\Models\NotificationModel;
use App\Models\SystemSettingModel;
use App\Services\Notification\NotificationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SendReminders extends BaseCommand
{
    protected $group       = 'Veronika';
    protected $name        = 'veronika:send-reminders';
    protected $description = 'Proses otomatis pengiriman pengingat jadwal (H-1, H-1 Jam) dan Link Zoom via WhatsApp.';

    public function run(array $params)
    {
        CLI::write('[SI VERONIKA Scheduler] Memulai proses pengecekan pengingat...', 'green');

        $db                  = \Config\Database::connect();
        $notifService        = new NotificationService();
        $notifModel          = new NotificationModel();
        $settingModel        = new SystemSettingModel();

        $todayDate           = date('Y-m-d');
        $tomorrowDate        = date('Y-m-d', strtotime('+1 day'));
        $currentTime         = date('H:i:s');
        $currentTimestamp    = time();

        $zoomOffsetMinutes   = (int)$settingModel->getVal('zoom_reminder_offset_minutes', 10);

        // 1. Check H-1 Reminders (Tomorrow's schedules)
        $h1Apps = $db->table('applications')
            ->select('applications.id, applications.registration_number, schedules.date, schedules.start_time')
            ->join('appointments', 'appointments.application_id = applications.id')
            ->join('schedules', 'schedules.id = appointments.schedule_id')
            ->where('schedules.date', $tomorrowDate)
            ->whereIn('applications.status', ['Disetujui', 'Terjadwal'])
            ->get()
            ->getResultArray();

        $h1Sent = 0;
        foreach ($h1Apps as $app) {
            $eventKey = "app_{$app['id']}_reminder_h1";
            if (!$notifModel->isEventDispatched($eventKey)) {
                $res = $notifService->sendH1Reminder($app['id']);
                if ($res['success']) {
                    $h1Sent++;
                    CLI::write("  -> [H-1] Terkirim ke permohonan #{$app['registration_number']}", 'yellow');
                }
            }
        }

        // 2. Check H-1 Hour Reminders (Today's schedules starting in 50 - 70 minutes)
        $todayApps = $db->table('applications')
            ->select('applications.id, applications.registration_number, schedules.date, schedules.start_time, appointments.zoom_url')
            ->join('appointments', 'appointments.application_id = applications.id')
            ->join('schedules', 'schedules.id = appointments.schedule_id')
            ->where('schedules.date', $todayDate)
            ->whereIn('applications.status', ['Disetujui', 'Terjadwal'])
            ->get()
            ->getResultArray();

        $h1hSent = 0;
        $zoomSent = 0;

        foreach ($todayApps as $app) {
            $scheduleTimestamp = strtotime($todayDate . ' ' . $app['start_time']);
            $diffMinutes = round(($scheduleTimestamp - $currentTimestamp) / 60);

            // H-1h: Between 50 and 70 minutes before schedule
            if ($diffMinutes >= 50 && $diffMinutes <= 70) {
                $eventKey = "app_{$app['id']}_reminder_h1h";
                if (!$notifModel->isEventDispatched($eventKey)) {
                    $res = $notifService->sendH1hReminder($app['id']);
                    if ($res['success']) {
                        $h1hSent++;
                        CLI::write("  -> [H-1 Jam] Terkirim ke #{$app['registration_number']}", 'yellow');
                    }
                }
            }

            // Menjelang Jadwal: Zoom Link Dispatch (e.g. <= 10 minutes before, until schedule start time + 15 min)
            if ($diffMinutes <= $zoomOffsetMinutes && $diffMinutes >= -15) {
                $eventKey = "app_{$app['id']}_zoom_link";
                if (!$notifModel->isEventDispatched($eventKey)) {
                    if (!empty($app['zoom_url'])) {
                        $res = $notifService->sendZoomLinkNotification($app['id']);
                        if ($res['success']) {
                            $zoomSent++;
                            CLI::write("  -> [Zoom Link] Otomatis terkirim ke #{$app['registration_number']}", 'green');
                        }
                    } else {
                        CLI::write("  -> [PERINGATAN] Link Zoom belum tersedia untuk #{$app['registration_number']} (Waktu mendekati)", 'red');
                    }
                }
            }
        }

        CLI::write("[SI VERONIKA Scheduler] Selesai. (H-1: {$h1Sent}, H-1h: {$h1hSent}, Zoom: {$zoomSent})", 'green');
    }
}
