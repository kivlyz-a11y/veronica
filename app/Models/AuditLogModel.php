<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'action',
        'module',
        'reference_id',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    public $timestamps = false;

    public function getLogs(int $limit = 50)
    {
        return $this->select('audit_logs.*, users.name as user_name, users.role as user_role')
                    ->join('users', 'users.id = audit_logs.user_id', 'left')
                    ->orderBy('audit_logs.id', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
