<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $model = new UserModel();
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        $user = $model->where('username', $username)
                     ->orWhere('email', $username)
                     ->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            $session->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role'],
                'isLoggedIn' => true,
            ]);
            return redirect()->to('/dashboard');
        }

        $session->setFlashdata('error', 'Username/email atau password salah');
        return redirect()->back();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
} 