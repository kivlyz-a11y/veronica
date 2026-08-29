<?php

namespace App\Services\Registration;

use CodeIgniter\Database\BaseConnection;

class RegistrationNumberService
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Generate unique sequential registration number: VER-YYYYMMDD-0001
     */
    public function generateRegistrationNumber(?string $date = null): string
    {
        $targetDate = $date ?? date('Y-m-d');
        $dateFormatted = date('Ymd', strtotime($targetDate));
        $prefix = "VER-{$dateFormatted}-";

        // Query the latest registration number for the given date prefix
        $row = $this->db->table('applications')
            ->select('registration_number')
            ->like('registration_number', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $sequence = 1;
        if ($row && !empty($row['registration_number'])) {
            $parts = explode('-', $row['registration_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $sequence = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%04d', $prefix, $sequence);
    }

    /**
     * Generate unique booking code
     */
    public function generateBookingCode(): string
    {
        do {
            $code = 'BK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $exists = $this->db->table('applications')
                ->where('booking_code', $code)
                ->countAllResults() > 0;
        } while ($exists);

        return $code;
    }
}
