<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\DiskonModel;

class AuthController extends BaseController
{
    protected $user;
    protected $diskon; // 2. Tambahkan properti untuk DiskonModel

    function __construct()
    {
        helper('form');
        $this->user = new UserModel();
        $this->diskon = new DiskonModel(); // 3. Inisialisasi DiskonModel
    }

    public function login()
    {
        if ($this->request->getPost()) {
            $rules = [
                'username' => 'required|min_length[6]',
                'password' => 'required|min_length[7]|numeric',
            ];

            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');

                $dataUser = $this->user->where(['username' => $username])->first();

                if ($dataUser) {
                    if (password_verify($password, $dataUser['password'])) {
                        
                        // --- AWAL BAGIAN BARU (Soal Nomer 2) ---
                        
                        // Cari diskon untuk hari ini
                        $today = date('Y-m-d');
                        $diskonHariIni = $this->diskon->where('tanggal', $today)->first();

                        // Siapkan data untuk session
                        $sessionData = [
                            'id' => $dataUser['id'], // Sebaiknya simpan juga ID user
                            'username' => $dataUser['username'],
                            'role' => $dataUser['role'],
                            'isLoggedIn' => TRUE,
                            'nominal_diskon' => null // Beri nilai default null
                        ];

                        // Jika diskon untuk hari ini ditemukan, tambahkan ke session
                        if ($diskonHariIni) {
                            $sessionData['nominal_diskon'] = $diskonHariIni['nominal'];
                        }

                        // --- AKHIR BAGIAN BARU ---

                        // Simpan semua data ke session
                        session()->set($sessionData);

                        return redirect()->to(base_url('/home'));
                    } else {
                        session()->setFlashdata('failed', 'Kombinasi Username & Password Salah');
                        return redirect()->back();
                    }
                } else {
                    session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', $this->validator->listErrors());
                return redirect()->back();
            }
        }

        return view('v_login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}