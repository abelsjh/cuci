<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->orderBy('id', 'ASC')->findAll();

        return view('users/index', [
            'title' => 'Kelola Pengguna',
            'users' => $users,
        ]);
    }

    public function create()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]|alpha_dash',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,kasir]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'      => trim((string) $this->request->getPost('name')),
            'username'  => trim((string) $this->request->getPost('username')),
            'password'  => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'      => (string) $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $id = $this->userModel->insert($data);

        AuditLogger::log(
            (int) session()->get('user_id'),
            'CREATE',
            'user',
            (int) $id,
            "Menambahkan pengguna baru: {$data['username']} ({$data['name']}, Peran: {$data['role']})"
        );

        return redirect()->to('/users')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]|alpha_dash",
            'role'     => 'required|in_list[admin,kasir]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'      => trim((string) $this->request->getPost('name')),
            'username'  => trim((string) $this->request->getPost('username')),
            'role'      => (string) $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->userModel->update($id, $data);

        AuditLogger::log(
            (int) session()->get('user_id'),
            'UPDATE',
            'user',
            $id,
            "Memperbarui profil pengguna '{$user['username']}'"
        );

        return redirect()->to('/users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function resetPassword(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'new_password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $newPassword = (string) $this->request->getPost('new_password');
        $this->userModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        AuditLogger::log(
            (int) session()->get('user_id'),
            'RESET_PASSWORD',
            'user',
            $id,
            "Mereset password pengguna '{$user['username']}'"
        );

        return redirect()->to('/users')->with('success', "Password untuk pengguna '{$user['username']}' berhasil direset.");
    }

    public function toggle(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Jangan izinkan menonaktifkan akun sendiri
        if ((int) $user['id'] === (int) session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif digunakan.');
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);

        $statusStr = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        AuditLogger::log(
            (int) session()->get('user_id'),
            'TOGGLE_STATUS',
            'user',
            $id,
            "Pengguna '{$user['username']}' {$statusStr}"
        );

        return redirect()->to('/users')->with('success', "Akun '{$user['username']}' berhasil {$statusStr}.");
    }
}
