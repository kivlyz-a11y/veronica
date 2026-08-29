<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'application_id',
        'type',
        'event_key',
        'recipient',
        'message',
        'status',
        'attempts',
        'sent_at',
        'error_message',
    ];

    protected $useTimestamps = true;

    /**
     * Check if an event notification has already been sent or is pending
     */
    public function isEventDispatched(string $eventKey): bool
    {
        return $this->where('event_key', $eventKey)
                    ->whereIn('status', ['sent', 'pending'])
                    ->countAllResults() > 0;
    }
}
