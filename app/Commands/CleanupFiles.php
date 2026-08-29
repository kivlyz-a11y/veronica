<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupFiles extends BaseCommand
{
    protected $group       = 'Veronika';
    protected $name        = 'veronika:cleanup-files';
    protected $description = 'Membersihkan file temporary/cache ekspor laporan yang sudah lama.';

    public function run(array $params)
    {
        CLI::write('[SI VERONIKA] Membersihkan file cache dan ekspor lama...', 'green');

        $cacheDir = WRITEPATH . 'cache/';
        $files = glob($cacheDir . 'laporan_veronika_*');
        $deleted = 0;

        $oneDayAgo = time() - 86400; // 24 hours

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $oneDayAgo) {
                unlink($file);
                $deleted++;
            }
        }

        CLI::write("[SI VERONIKA] Selesai. {$deleted} file sementara dihapus.", 'green');
    }
}
