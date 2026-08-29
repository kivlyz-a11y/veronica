<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ApplicationModel;
use App\Models\AuditLogModel;
use App\Models\DocumentModel;
use App\Models\NotificationModel;
use App\Models\ServiceModel;
use App\Services\Audit\AuditService;
use App\Services\Notification\NotificationService;

class ApplicationController extends BaseController
{
    protected ApplicationModel $applicationModel;
    protected ServiceModel $serviceModel;
    protected DocumentModel $documentModel;
    protected NotificationModel $notificationModel;
    protected AuditLogModel $auditLogModel;
    protected AuditService $auditService;
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->applicationModel    = new ApplicationModel();
        $this->serviceModel        = new ServiceModel();
        $this->documentModel       = new DocumentModel();
        $this->notificationModel   = new NotificationModel();
        $this->auditLogModel       = new AuditLogModel();
        $this->auditService        = new AuditService();
        $this->notificationService = new NotificationService();
    }

    public function index()
    {
        helper(['form', 'veronika']);

        $filters = [
            'search'     => $this->request->getGet('search'),
            'status'     => $this->request->getGet('status'),
            'service_id' => $this->request->getGet('service_id'),
            'date_from'  => $this->request->getGet('date_from'),
            'date_to'    => $this->request->getGet('date_to'),
        ];

        $paginated = $this->applicationModel->getFiltered($filters, 15);
        $services  = $this->serviceModel->findAll();

        $data = [
            'title'        => 'Manajemen Permohonan Layanan - SI VERONIKA',
            'applications' => $paginated['data'],
            'pager'        => $paginated['pager'],
            'services'     => $services,
            'filters'      => $filters,
        ];

        return view('admin/applications/index', $data);
    }

    public function show(int $id)
    {
        helper(['form', 'veronika']);

        $app = $this->applicationModel->getDetailed($id);
        if (!$app) {
            return redirect()->to(site_url('admin/applications'))->with('error', 'Permohonan tidak ditemukan.');
        }

        $documents     = $this->documentModel->getByApplicationId($id);
        $notifications = $this->notificationModel->where('application_id', $id)->orderBy('id', 'DESC')->findAll();
        $auditLogs     = $this->auditLogModel->where('module', 'applications')
                                             ->where('reference_id', $app['registration_number'])
                                             ->orderBy('id', 'DESC')
                                             ->findAll();

        $data = [
            'title'         => "Detail Permohonan #{$app['registration_number']} - SI VERONIKA",
            'app'           => $app,
            'documents'     => $documents,
            'notifications' => $notifications,
            'auditLogs'     => $auditLogs,
        ];

        return view('admin/applications/show', $data);
    }

    /**
     * Update application status (Verifikasi)
     */
    public function updateStatus(int $id)
    {
        $app = $this->applicationModel->find($id);
        if (!$app) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }

        $newStatus         = $this->request->getPost('status');
        $verificationNotes = $this->request->getPost('verification_notes');
        $sendWa            = (bool)$this->request->getPost('send_whatsapp');

        $userId = session()->get('user_id');
        $oldStatus = $app['status'];

        $updateData = [
            'status'             => $newStatus,
            'verification_notes' => $verificationNotes,
            'verified_by'        => $userId,
            'verified_at'        => date('Y-m-d H:i:s'),
        ];

        $this->applicationModel->update($id, $updateData);

        // Audit Log
        $this->auditService->log(
            $userId,
            'update_status',
            'applications',
            $app['registration_number'],
            "Mengubah status dari '{$oldStatus}' menjadi '{$newStatus}'. Catatan: " . ($verificationNotes ?: '-')
        );

        // Send WhatsApp notification if requested
        if ($sendWa) {
            try {
                $this->notificationService->sendStatusUpdateNotification($id);
            } catch (\Exception $e) {
                log_message('error', '[ApplicationController] Failed to send status WA: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('message', "Status permohonan berhasil diperbarui menjadi '{$newStatus}'.");
    }

    /**
     * Resend notification
     */
    public function resendNotification(int $id, string $type)
    {
        $app = $this->applicationModel->find($id);
        if (!$app) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }

        $res = ['success' => false, 'message' => 'Tipe notifikasi tidak dikenal.'];

        switch ($type) {
            case 'confirmation':
                $res = $this->notificationService->sendRegistrationConfirmation($id);
                break;
            case 'status':
                $res = $this->notificationService->sendStatusUpdateNotification($id);
                break;
            case 'zoom':
                $res = $this->notificationService->sendZoomLinkNotification($id, true);
                break;
            case 'h1':
                $res = $this->notificationService->sendH1Reminder($id);
                break;
            case 'h1h':
                $res = $this->notificationService->sendH1hReminder($id);
                break;
        }

        if ($res['success']) {
            return redirect()->back()->with('message', 'Notifikasi WhatsApp berhasil dikirim ulang.');
        }

        return redirect()->back()->with('error', 'Gagal mengirim notifikasi: ' . $res['message']);
    }
}
