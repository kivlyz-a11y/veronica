<?php

namespace App\Models;

use CodeIgniter\Model;

class HolidayModel extends Model
{
    protected $table            = 'holidays';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'holiday_date',
        'name',
        'status',
    ];

    protected $useTimestamps = true;

    public function isHoliday(string $date): bool
    {
        return $this->where('holiday_date', $date)
                    ->where('status', 'active')
                    ->countAllResults() > 0;
    }
}
