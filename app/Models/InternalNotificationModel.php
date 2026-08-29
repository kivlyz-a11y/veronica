<?php

namespace App\Models;

use CodeIgniter\Model;

class InternalNotificationModel extends Model
{
    protected $table            = 'internal_notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'title',
        'message',
        'url',
        'is_read',
    ];

    public $timestamps = false;

    public function getUnreadForUser(?int $userId = null, int $limit = 10)
    {
        $builder = $this->where('is_read', 0);
        if ($userId !== null) {
            $builder->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('user_id', null)
                    ->groupEnd();
        }
        return $builder->orderBy('id', 'DESC')->limit($limit)->findAll();
    }

    public function countUnread(?int $userId = null): int
    {
        $builder = $this->where('is_read', 0);
        if ($userId !== null) {
            $builder->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('user_id', null)
                    ->groupEnd();
        }
        return $builder->countAllResults();
    }
}
