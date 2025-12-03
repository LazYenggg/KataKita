<?php namespace App\Controllers;
use App\Models\UserModel;

class Auth extends BaseController {
    public function login() {
        return view('auth/login');
    }

    public function process_login() {
        $model = new UserModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $user = $model->where('username', $username)->first();
        
        if($user && password_verify($password, $user['password'])) {
            session()->set([
                'user_id' => $user['id'],
                'name' => $user['name'],
                'is_logged_in' => true
            ]);
            return redirect()->to('/');
        }
        return redirect()->back()->with('error', 'Username atau Password salah');
    }

    public function register() {
        return view('auth/register');
    }

    public function process_register() {
        // PERBAIKAN DI SINI: Gunakan kurung siku [] bukan ()
        $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'password' => 'required|min_length[4]', 
            'name'     => 'required'
        ];

        // Validasi input
        if(!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan ke halaman daftar dengan pesan error
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $model->save([
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'name'     => $this->request->getVar('name')
        ]);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('/login');
    }
}