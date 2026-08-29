<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\Audit\AuditService;

class UserController extends BaseController
{
    protected UserModel $userModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->auditService = new AuditService();
    }

    public function index()
    {
        helper(['form', 'veronika']);

        $users = $this->userModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title' => 'Manajemen Pengguna & Petugas - SI VERONIKA',
            'users' => $users,
        ];

        return view('admin/users/index', $data);
    }

    public function store()
    {
        $rules = [
            'name'     => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[superadmin,admin,officer,pimpinan]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $password = $this->request->getPost('password');

        $this->userModel->insert([
            'name'          => trim($this->request->getPost('name')),
            'email'         => trim($this->request->getPost('email')),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => $this->request->getPost('role'),
            'phone'         => trim($this->request->getPost('phone')),
            'status'        => 'active',
        ]);

        $this->auditService->log(
            session()->get('user_id'),
            'create_user',
            'users',
            $this->request->getPost('email'),
            "Menambahkan pengguna baru: {$this->request->getPost('name')} ({$this->request->getPost('role')})."
        );

        return redirect()->back()->with('message', 'Pengguna berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'  => 'required|in_list[superadmin,admin,officer,pimpinan]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name'   => trim($this->request->getPost('name')),
            'email'  => trim($this->request->getPost('email')),
            'role'   => $this->request->getPost('role'),
            'phone'  => trim($this->request->getPost('phone')),
            'status' => $this->request->getPost('status') ?: 'active',
        ];

        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $updateData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $updateData);

        $this->auditService->log(
            session()->get('user_id'),
            'update_user',
            'users',
            (string)$id,
            "Memperbarui data pengguna ID #{$id} ({$updateData['name']})."
        );

        return redirect()->back()->with('message', 'Data pengguna berhasil diperbarui.');
    }
}
