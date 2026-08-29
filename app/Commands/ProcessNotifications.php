<?php

namespace App\Commands;

use App\Models\NotificationModel;
use App\Services\WhatsApp\WhatsAppService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProcessNotifications extends BaseCommand
{
    protected $group       = 'Veronika';
    protected $name        = 'veronika:process-notifications';
    protected $description = 'Memproses dan mencoba ulang pengiriman notifikasi WhatsApp yang tertunda (pending) atau gagal.';

    public function run(array $params)
    {
        CLI::write('[SI VERONIKA] Memproses notifikasi WhatsApp yang tertunda...', 'green');

        $notifModel = new NotificationModel();
        $waService  = new WhatsAppService();

        $pending = $notifModel->whereIn('status', ['pending', 'failed'])
                              ->where('attempts <', 3)
                              ->orderBy('id', 'ASC')
                              ->limit(20)
                              ->findAll();

        if (empty($pending)) {
            CLI::write('Tidak ada notifikasi yang perlu diproses.', 'yellow');
            return;
        }

        $processed = 0;
        foreach ($pending as $notif) {
            CLI::write("  -> Mencoba kirim notifikasi #{$notif['id']} ke {$notif['recipient']}...");
            $res = $waService->send(
                $notif['recipient'],
                $notif['message'],
                $notif['type'],
                $notif['application_id'],
                $notif['event_key']
            );

            if ($res['success']) {
                $processed++;
                CLI::write("     [SUKSES] Notifikasi #{$notif['id']} berhasil terkirim.", 'green');
            } else {
                CLI::write("     [GAGAL] Notifikasi #{$notif['id']}: {$res['message']}", 'red');
            }
        }

        CLI::write("[SI VERONIKA] Selesai memproses {$processed}/" . count($pending) . ' notifikasi.', 'green');
    }
}
