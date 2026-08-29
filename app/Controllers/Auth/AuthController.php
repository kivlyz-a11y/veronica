<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\Audit\AuditService;

class AuthController extends BaseController
{
    protected UserModel $userModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->auditService = new AuditService();
    }

    public function login()
    {
        helper(['form', 'veronika']);

        if (session()->get('is_logged_in')) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return view('auth/login', ['title' => 'Login Petugas - SI VERONIKA']);
    }

    public function processLogin()
    {
        helper(['form', 'veronika']);

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau Password salah.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Hubungi Administrator.');
        }

        // Regenerate session id to protect against session fixation
        session()->regenerate();

        $sessionData = [
            'user_id'      => $user['id'],
            'user_name'    => $user['name'],
            'user_email'   => $user['email'],
            'user_role'    => $user['role'],
            'is_logged_in' => true,
        ];
        session()->set($sessionData);

        // Update last login
        $this->userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        $this->auditService->log(
            $user['id'],
            'login',
            'auth',
            (string)$user['id'],
            "User {$user['name']} ({$user['role']}) berhasil login."
        );

        $redirectUrl = session()->get('redirect_url');
        if (!empty($redirectUrl)) {
            session()->remove('redirect_url');
            return redirect()->to($redirectUrl);
        }

        if ($user['role'] === 'officer') {
            return redirect()->to(site_url('admin/applications'));
        }

        return redirect()->to(site_url('admin/dashboard'));
    }

    public function logout()
    {
        $userId   = session()->get('user_id');
        $userName = session()->get('user_name');

        if ($userId) {
            $this->auditService->log(
                $userId,
                'logout',
                'auth',
                (string)$userId,
                "User {$userName} logout dari sistem."
            );
        }

        session()->destroy();
        return redirect()->to(site_url('auth/login'))->with('message', 'Anda telah berhasil keluar dari sistem.');
    }
}
