<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupFiles extends BaseCommand
{
    protected $group       = 'Veronica';
    protected $name        = 'veronica:cleanup-files';
    protected $description = 'Membersihkan file temporary/cache ekspor laporan yang sudah lama.';

    public function run(array $params)
    {
        CLI::write('[SI VERONICA] Membersihkan file cache dan ekspor lama...', 'green');

        $cacheDir = WRITEPATH . 'cache/';
        $files = glob($cacheDir . 'laporan_veroni*_*');
        $deleted = 0;

        $oneDayAgo = time() - 86400; // 24 hours

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $oneDayAgo) {
                unlink($file);
                $deleted++;
            }
        }

        CLI::write("[SI VERONICA] Selesai. {$deleted} file sementara dihapus.", 'green');
    }
}
