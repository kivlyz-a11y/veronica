<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\ApplicationModel;
use App\Models\AuditLogModel;

class StatusController extends BaseController
{
    protected ApplicationModel $applicationModel;
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->auditLogModel    = new AuditLogModel();
    }

    public function index()
    {
        helper(['form', 'veronika']);

        $regNum = $this->request->getGet('nomor_registrasi');
        $phone  = $this->request->getGet('whatsapp');

        $application = null;
        $searched    = false;

        if (!empty($regNum) && !empty($phone)) {
            $searched = true;
            $application = $this->applicationModel->findByRegistrationAndPhone($regNum, $phone);
        }

        $data = [
            'title'       => 'Cek Status Permohonan - SI VERONIKA',
            'application' => $application,
            'searched'    => $searched,
            'regNum'      => $regNum,
            'phone'       => $phone,
        ];

        return view('public/status', $data);
    }

    public function search()
    {
        $regNum = trim($this->request->getPost('registration_number'));
        $phone  = trim($this->request->getPost('phone'));

        if (empty($regNum) || empty($phone)) {
            return redirect()->to(site_url('cek-status'))->with('error', 'Nomor Registrasi dan Nomor WhatsApp wajib diisi.');
        }

        return redirect()->to(site_url("cek-status?nomor_registrasi=" . urlencode($regNum) . "&whatsapp=" . urlencode($phone)));
    }
}
