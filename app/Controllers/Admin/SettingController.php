<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SystemSettingModel;
use App\Services\Audit\AuditService;
use App\Services\WhatsApp\WhatsAppService;

class SettingController extends BaseController
{
    protected SystemSettingModel $settingModel;
    protected AuditService $auditService;
    protected WhatsAppService $waService;

    public function __construct()
    {
        $this->settingModel = new SystemSettingModel();
        $this->auditService = new AuditService();
        $this->waService    = new WhatsAppService();
    }

    public function index()
    {
        helper(['form', 'veronika']);

        $settings = $this->settingModel->getAllAsMap();

        $data = [
            'title'    => 'Pengaturan Sistem & WhatsApp Gateway - SI VERONIKA',
            'settings' => $settings,
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $posts = $this->request->getPost();

        foreach ($posts as $key => $val) {
            if ($key === 'csrf_test_name') continue;
            $this->settingModel->setVal($key, (string)$val);
        }

        $this->auditService->log(
            session()->get('user_id'),
            'update_settings',
            'settings',
            'system',
            'Memperbarui konfigurasi sistem dan template WhatsApp.'
        );

        return redirect()->back()->with('message', 'Pengaturan sistem berhasil disimpan.');
    }

    /**
     * Test WhatsApp Gateway connection & test message
     */
    public function testWhatsApp()
    {
        $targetPhone = trim((string)$this->request->getPost('test_phone'));
        $testMsg     = "Tes koneksi WhatsApp Gateway SI VERONIKA Pengadilan Agama Penajam berhasil pada " . date('d/m/Y H:i:s') . ' WITA.';

        if (empty($targetPhone)) {
            $connRes = $this->waService->testConnection();
            if (!empty($connRes['success'])) {
                return redirect()->back()->with('message', $connRes['message']);
            }
            return redirect()->back()->with('error', "Koneksi Gateway Gagal: " . ($connRes['message'] ?? 'Tidak dapat terhubung ke server.'));
        }

        $res = $this->waService->send($targetPhone, $testMsg, 'wa_test', null, 'test_' . time());

        if ($res['success']) {
            return redirect()->back()->with('message', "Pesan tes berhasil dikirim ke {$targetPhone}.");
        }

        return redirect()->back()->with('error', "Gagal mengirim pesan tes: {$res['message']}");
    }
}
