<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriProductModel;

class KategoriProduct extends BaseController
{
    protected $kategoriProduct;

    public function __construct()
    {
        $this->kategoriProduct = new KategoriProductModel();
    }

    // Menampilkan semua kategori
    public function index()
    {
        $data['kategori'] = $this->kategoriProduct->select('id, nama')->findAll();
        return view('kategori_produk', $data);
    }

    // Menyimpan kategori baru
    public function create()
    {
        $dataForm = [
            'username' => $this->request->getPost('username'),
            'created_at' => date("Y-m-d H:i:s")
        ];

        $this->kategoriProduct->insert($dataForm);

        return redirect()->to(base_url('kategoriproduct'))->with('success', 'Data berhasil ditambahkan');
    }

    // Menyimpan perubahan kategori
    public function edit($id)
    {
        $dataForm = [
            'username' => $this->request->getPost('username'),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $this->kategoriProduct->update($id, $dataForm);

        return redirect()->to(base_url('kategoriproduct'))->with('success', 'Data berhasil diubah');
    }

    // Menghapus kategori
    public function delete($id)
    {
        $this->kategoriProduct->delete($id);

        return redirect()->to(base_url('kategoriproduct'))->with('success', 'Data berhasil dihapus');
        $this->db->table('kategori_produk')->get()->getResult();
    }
    
}
