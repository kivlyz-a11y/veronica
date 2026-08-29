<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ApplicationModel;
use App\Models\AppointmentModel;
use App\Services\Audit\AuditService;
use App\Services\Notification\NotificationService;

class ZoomController extends BaseController
{
    protected ApplicationModel $applicationModel;
    protected AppointmentModel $appointmentModel;
    protected AuditService $auditService;
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->applicationModel    = new ApplicationModel();
        $this->appointmentModel    = new AppointmentModel();
        $this->auditService        = new AuditService();
        $this->notificationService = new NotificationService();
    }

    /**
     * Store or update Zoom meeting link manually
     */
    public function save(int $applicationId)
    {
        $app = $this->applicationModel->getDetailed($applicationId);
        if (!$app) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }

        $rules = [
            'zoom_url' => 'required|valid_url',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Link Zoom wajib berupa URL yang valid (misal: https://zoom.us/j/123456789).');
        }

        $zoomUrl      = trim($this->request->getPost('zoom_url'));
        $meetingId    = trim($this->request->getPost('zoom_meeting_id'));
        $zoomPassword = trim($this->request->getPost('zoom_password'));
        $zoomNotes    = trim($this->request->getPost('zoom_notes'));
        $sendWaNow    = (bool)$this->request->getPost('send_wa_now');

        $userId = session()->get('user_id');

        $appointment = $this->appointmentModel->where('application_id', $applicationId)->first();
        if (!$appointment) {
            return redirect()->back()->with('error', 'Data jadwal/appointment tidak ditemukan.');
        }

        $oldZoom = $appointment['zoom_url'];

        $this->appointmentModel->update($appointment['id'], [
            'zoom_url'        => $zoomUrl,
            'zoom_meeting_id' => $meetingId,
            'zoom_password'   => $zoomPassword,
            'zoom_notes'      => $zoomNotes,
            'zoom_added_by'   => $userId,
            'zoom_added_at'   => date('Y-m-d H:i:s'),
        ]);

        // Audit Log
        $actionDesc = empty($oldZoom) ? "Menambahkan Link Zoom: {$zoomUrl}" : "Memperbarui Link Zoom dari '{$oldZoom}' menjadi '{$zoomUrl}'";
        $this->auditService->log(
            $userId,
            'update_zoom_link',
            'applications',
            $app['registration_number'],
            $actionDesc
        );

        // Optionally send WhatsApp Zoom link immediately
        if ($sendWaNow) {
            $waRes = $this->notificationService->sendZoomLinkNotification($applicationId, true);
            if ($waRes['success']) {
                return redirect()->back()->with('message', 'Link Zoom berhasil disimpan dan dikirimkan ke pemohon via WhatsApp.');
            } else {
                return redirect()->back()->with('warning', 'Link Zoom berhasil disimpan, namun WhatsApp gagal terkirim: ' . $waRes['message']);
            }
        }

        return redirect()->back()->with('message', 'Link Zoom berhasil disimpan.');
    }
}
