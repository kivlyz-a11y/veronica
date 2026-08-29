<?php

namespace App\Services\Audit;

use App\Models\AuditLogModel;

class AuditService
{
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * Log user or system action
     */
    public function log(
        ?int $userId,
        string $action,
        string $module,
        ?string $referenceId,
        string $description
    ): void {
        $request = \Config\Services::request();
        $ip = $request->getIPAddress();
        $ua = (string)$request->getUserAgent();

        $this->auditLogModel->insert([
            'user_id'      => $userId ?? (session()->get('user_id') ?? null),
            'action'       => $action,
            'module'       => $module,
            'reference_id' => $referenceId,
            'description'  => $description,
            'ip_address'   => $ip,
            'user_agent'   => $ua,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
