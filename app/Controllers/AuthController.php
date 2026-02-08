<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

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

        $userModel = new UserModel();
        $userModel->insert([
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'masyarakat',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}

