<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditLogController extends BaseController
{
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }

    public function index()
    {
        helper('veronika');

        $logs = $this->auditLogModel->getLogs(100);

        $data = [
            'title' => 'Riwayat Log Aktivitas (Audit Trail) - SI VERONIKA',
            'logs'  => $logs,
        ];

        return view('admin/audit_logs/index', $data);
    }
}
