<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\BrevoService;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole();
        }
        return view('auth/login');
    }

    private function redirectByRole()
    {
        $role = session()->get('role');
        if ($role === 'admin') {
            return redirect()->to('/admin');
        }
        return redirect()->to('/');
    }

    public function authenticate()
    {
        $userModel = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Check email verification for non-admin users
            if ($user['role'] !== 'admin' && !$user['email_verified']) {
                return redirect()->back()->with('error', 'Email belum diverifikasi. Silakan cek inbox email Anda.');
            }

            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'nama' => $user['nama'] ?? $user['username'],
                'role' => $user['role'] ?? 'masyarakat',
                'logged_in' => true,
            ]);
            return $this->redirectByRole();
        }

        return redirect()->back()->with('error', 'Username atau password salah');
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole();
        }
        return view('auth/register');
    }

    public function registerSave()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nama' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ], [
            'nama' => [
                'required' => 'Nama lengkap wajib diisi',
                'min_length' => 'Nama minimal 3 karakter',
            ],
            'email' => [
                'required' => 'Email wajib diisi',
                'valid_email' => 'Format email tidak valid',
                'is_unique' => 'Email sudah terdaftar',
            ],
            'username' => [
                'required' => 'Username wajib diisi',
                'min_length' => 'Username minimal 3 karakter',
                'is_unique' => 'Username sudah digunakan',
            ],
            'password' => [
                'required' => 'Password wajib diisi',
                'min_length' => 'Password minimal 6 karakter',
            ],
            'password_confirm' => [
                'required' => 'Konfirmasi password wajib diisi',
                'matches' => 'Konfirmasi password tidak cocok',
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Generate verification token
        $token = bin2hex(random_bytes(32));
        $tokenExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $userModel = new UserModel();
        $email = $this->request->getPost('email');
        $nama = $this->request->getPost('nama');

        $userModel->insert([
            'nama' => $nama,
            'email' => $email,
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'masyarakat',
            'email_verified' => 0,
            'verification_token' => $token,
            'token_expires_at' => $tokenExpires,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Send verification email
        $brevo = new BrevoService();
        $emailSent = $brevo->sendVerificationEmail($email, $nama, $token);

        if (!$emailSent) {
            // Log error but don't fail registration
            log_message('error', 'Failed to send verification email to: ' . $email);
        }

        // Store email in session for resend functionality
        session()->setFlashdata('pending_email', $email);

        return redirect()->to('/verify-pending');
    }

    public function verifyPending()
    {
        return view('auth/verify_pending');
    }

    public function verifyEmail($token)
    {
        $userModel = new UserModel();
        $user = $userModel->where('verification_token', $token)->first();

        if (!$user) {
            return view('auth/verify_result', [
                'success' => false,
                'message' => 'Token verifikasi tidak valid.'
            ]);
        }

        // Check if token expired
        if (strtotime($user['token_expires_at']) < time()) {
            return view('auth/verify_result', [
                'success' => false,
                'message' => 'Token verifikasi sudah kadaluarsa. Silakan minta kirim ulang.'
            ]);
        }

        // Verify the email
        $userModel->update($user['id'], [
            'email_verified' => 1,
            'verification_token' => null,
            'token_expires_at' => null,
        ]);

        return view('auth/verify_result', [
            'success' => true,
            'message' => 'Email berhasil diverifikasi! Silakan login.'
        ]);
    }

    public function resendVerification()
    {
        $email = $this->request->getGet('email') ?? session()->getFlashdata('pending_email');

        if (!$email) {
            return redirect()->to('/login')->with('error', 'Email tidak ditemukan.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Email tidak terdaftar.');
        }

        if ($user['email_verified']) {
            return redirect()->to('/login')->with('success', 'Email sudah terverifikasi. Silakan login.');
        }

        // Generate new token
        $token = bin2hex(random_bytes(32));
        $tokenExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $userModel->update($user['id'], [
            'verification_token' => $token,
            'token_expires_at' => $tokenExpires,
        ]);

        // Send verification email
        $brevo = new BrevoService();
        $emailSent = $brevo->sendVerificationEmail($email, $user['nama'], $token);

        session()->setFlashdata('pending_email', $email);

        if ($emailSent) {
            return redirect()->to('/verify-pending')->with('success', 'Email verifikasi telah dikirim ulang.');
        }

        return redirect()->to('/verify-pending')->with('error', 'Gagal mengirim email. Silakan coba lagi.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}


