<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->is('post')) {
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]',
                'password' => 'required|min_length[4]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $username = trim((string) $this->request->getPost('username'));
            $password = (string) $this->request->getPost('password');

            $user = $this->userModel->findByUsername($username);

            if (! $user || ! password_verify($password, $user['password'])) {
                // Catat failed login count di session
                $failedAttempts = (int) session()->get('failed_logins_' . $username) + 1;
                session()->set('failed_logins_' . $username, $failedAttempts);

                if ($failedAttempts >= 3) {
                    AuditLogger::log(
                        $user ? (int) $user['id'] : null,
                        'LOGIN_FAILED',
                        'auth',
                        null,
                        "Percobaan login gagal berulang kali ({$failedAttempts}x) untuk username '{$username}' dari IP " . $this->request->getIPAddress()
                    );
                }

                return redirect()->back()->withInput()->with('error', 'Username atau password tidak sesuai.');
            }

            if (! (bool) $user['is_active']) {
                return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.');
            }

            // Reset failed logins counter
            session()->remove('failed_logins_' . $username);

            // Set session data
            session()->set([
                'user_id'    => (int) $user['id'],
                'user_name'  => $user['name'],
                'user_login' => $user['username'],
                'user_role'  => $user['role'],
                'isLoggedIn' => true,
            ]);

            return redirect()->to('/dashboard')->with('success', 'Selamat datang, ' . $user['name'] . '!');
        }

        return view('auth/login', [
            'title' => 'Masuk',
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
