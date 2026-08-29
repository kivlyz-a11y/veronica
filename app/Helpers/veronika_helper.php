<?php

use App\Models\SystemSettingModel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (!function_exists('get_setting')) {
    function get_setting(string $key, $default = '')
    {
        static $settings = null;
        if ($settings === null) {
            $model = new SystemSettingModel();
            $settings = $model->getAllAsMap();
        }
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('format_indo_date')) {
    function format_indo_date(?string $datetime, bool $withTime = false): string
    {
        if (empty($datetime)) {
            return '-';
        }

        $timestamp = strtotime($datetime);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $days = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        $dayName = $days[(int)date('N', $timestamp)];
        $d = date('j', $timestamp);
        $m = $months[(int)date('n', $timestamp)];
        $y = date('Y', $timestamp);

        if ($withTime) {
            $time = date('H.i', $timestamp);
            return "{$dayName}, {$d} {$m} {$y} pukul {$time} WITA";
        }

        return "{$dayName}, {$d} {$m} {$y}";
    }
}

if (!function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        $map = [
            'Menunggu Verifikasi' => ['class' => 'bg-warning text-dark', 'icon' => 'bi-clock-history'],
            'Sedang Diverifikasi' => ['class' => 'bg-info text-white',    'icon' => 'bi-search'],
            'Disetujui'           => ['class' => 'bg-primary text-white', 'icon' => 'bi-check-circle'],
            'Perlu Perbaikan'     => ['class' => 'bg-warning text-dark', 'icon' => 'bi-exclamation-triangle'],
            'Ditolak'             => ['class' => 'bg-danger text-white',  'icon' => 'bi-x-circle'],
            'Terjadwal'           => ['class' => 'bg-primary text-white', 'icon' => 'bi-calendar-check'],
            'Sedang Berlangsung'  => ['class' => 'bg-info text-white',    'icon' => 'bi-camera-video'],
            'Selesai'             => ['class' => 'bg-success text-white', 'icon' => 'bi-patch-check'],
            'Dibatalkan'          => ['class' => 'bg-secondary text-white', 'icon' => 'bi-slash-circle'],
            'Tidak Hadir'         => ['class' => 'bg-dark text-white',    'icon' => 'bi-person-x'],
        ];

        $conf = $map[$status] ?? ['class' => 'bg-secondary text-white', 'icon' => 'bi-info-circle'];
        return "<span class=\"badge {$conf['class']} rounded-pill px-3 py-2\"><i class=\"bi {$conf['icon']} me-1\"></i>{$status}</span>";
    }
}

if (!function_exists('mask_nik')) {
    function mask_nik(?string $nik): string
    {
        if (empty($nik) || strlen($nik) < 8) {
            return '****************';
        }
        return substr($nik, 0, 6) . '******' . substr($nik, -4);
    }
}

if (!function_exists('mask_phone')) {
    function mask_phone(?string $phone): string
    {
        if (empty($phone) || strlen($phone) < 6) {
            return '**********';
        }
        return substr($phone, 0, 4) . '****' . substr($phone, -3);
    }
}

if (!function_exists('generate_qr_data_uri')) {
    function generate_qr_data_uri(string $data): string
    {
        try {
            $options = new QROptions([
                'outputType'     => QRCode::OUTPUT_MARKUP_SVG,
                'svgAddXmlHeader'=> false,
                'scale'          => 5,
            ]);
            $qrcode = new QRCode($options);
            return $qrcode->render($data);
        } catch (\Throwable $e) {
            return '';
        }
    }
}

if (!function_exists('format_filesize')) {
    function format_filesize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
