<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table            = 'appointments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'application_id',
        'schedule_id',
        'zoom_url',
        'zoom_meeting_id',
        'zoom_password',
        'zoom_notes',
        'zoom_added_by',
        'zoom_added_at',
        'check_in_at',
        'check_in_by',
        'service_started_at',
        'service_ended_at',
        'status',
    ];

    protected $useTimestamps = true;
}
