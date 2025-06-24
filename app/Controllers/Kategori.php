<?php

namespace App\Controllers;


use App\Controllers\BaseController;
class Kategori extends BaseController
{
    public function index()
    {
        return view('kategori_view');
    }

    public function detail($nama_kategori)
    {
        // Kirim data kategori ke view
        $data['kategori'] = urldecode($nama_kategori); // Decode jika ada spasi atau karakter khusus
        return view('kategori_detail', $data);
    }
}
