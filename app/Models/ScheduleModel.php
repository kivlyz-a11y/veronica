<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table            = 'schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'date',
        'start_time',
        'end_time',
        'capacity',
        'booked',
        'assigned_officer_id',
        'status',
        'notes',
    ];

    protected $useTimestamps = true;

    /**
     * Get available slots for a specific date
     */
    public function getAvailableSlots(string $date)
    {
        $builder = $this->db->table($this->table);
        $builder->select('schedules.*, users.name as officer_name');
        $builder->join('users', 'users.id = schedules.assigned_officer_id', 'left');
        $builder->where('schedules.date', $date);
        $builder->where('schedules.status', 'active');
        $builder->orderBy('schedules.start_time', 'ASC');
        
        $slots = $builder->get()->getResultArray();
        
        // Add computed available flag and current time check if today
        $nowTime = date('H:i:s');
        $todayDate = date('Y-m-d');
        
        foreach ($slots as &$slot) {
            $isPastTime = ($date === $todayDate && $slot['start_time'] <= $nowTime);
            $isFull = ($slot['booked'] >= $slot['capacity']);
            
            $slot['is_available'] = (!$isPastTime && !$isFull);
            $slot['is_full'] = $isFull;
            $slot['is_past'] = $isPastTime;
            $slot['remaining_quota'] = max(0, $slot['capacity'] - $slot['booked']);
            $slot['time_formatted'] = substr($slot['start_time'], 0, 5) . ' - ' . substr($slot['end_time'], 0, 5);
        }
        
        return $slots;
    }
}
