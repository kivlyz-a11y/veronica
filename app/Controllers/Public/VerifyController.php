<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\ApplicationModel;

class VerifyController extends BaseController
{
    public function verify(string $regNum, string $bookingCode)
    {
        helper('veronika');

        $appModel = new ApplicationModel();
        $app = $appModel->where('registration_number', $regNum)
                        ->where('booking_code', $bookingCode)
                        ->first();

        if (!$app) {
            return redirect()->to(site_url('/'))->with('error', 'Kode QR Tiket tidak valid atau tidak ditemukan.');
        }

        // If user is logged in as officer/admin, redirect directly to application detail
        if (session()->get('is_logged_in')) {
            return redirect()->to(site_url("admin/applications/{$app['id']}"));
        }

        // Otherwise show public status
        $detailed = $appModel->getDetailed($app['id']);
        $data = [
            'title'       => 'Verifikasi Tiket Pelayanan - SI VERONIKA',
            'application' => $detailed,
            'searched'    => true,
            'regNum'      => $regNum,
            'phone'       => '',
        ];

        return view('public/status', $data);
    }
}
