<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use App\Models\DiskonModel;

class TransaksiController extends BaseController
{
    protected $session;
    protected $client;
    protected $apiKey;
    protected $transaction;
    protected $transaction_detail;
    protected $diskon;

    /**
     * Constructor.
     * Menggunakan session bawaan untuk keranjang.
     */
    public function __construct()
    {
        helper('number');
        helper('form');

        // Inisialisasi session dan model
        $this->session = \Config\Services::session();
        $this->client = new \GuzzleHttp\Client();
        $this->apiKey = env('COST_KEY');
        $this->transaction = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
        $this->diskon = new DiskonModel();
    }

    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function index()
    {
        $items = $this->session->get('cart') ?? [];
        $total = 0;
        $total_diskon = 0;

        foreach ($items as $item) {
            $harga_setelah_diskon = $item['price'] - ($item['options']['diskon'] ?? 0);
            $total += $harga_setelah_diskon * $item['qty'];
            $total_diskon += ($item['options']['diskon'] ?? 0) * $item['qty'];
        }

        $data['items'] = $items;
        $data['total'] = $total; // Ini adalah total akhir setelah diskon
        $data['total_diskon'] = $total_diskon;

        return view('v_keranjang', $data);
    }

    /**
     * Menambahkan produk ke keranjang menggunakan session.
     * Logika ini sudah diperbaiki untuk menangani penambahan produk yang sudah ada.
     */
    public function cart_add()
    {
        $id_produk = $this->request->getPost('id');
        $harga_asli = $this->request->getPost('harga');
        $tanggal_hari_ini = date('Y-m-d');

        // Cari diskon
        $diskon = $this->diskon
            ->where('id_produk', $id_produk)
            ->where('tanggal', $tanggal_hari_ini)
            ->first();
        $nilai_diskon = $diskon['nominal'] ?? 0;

        // Ambil keranjang dari session
        $cart = $this->session->get('cart') ?? [];

        // Cek apakah produk sudah ada di keranjang
        $product_exists = false;
        foreach ($cart as &$item) { // Menggunakan reference (&) agar bisa diubah langsung
            if ($item['id'] == $id_produk) {
                // Jika produk sudah ada, tambahkan jumlahnya
                $item['qty']++;
                $product_exists = true;
                break;
            }
        }
        unset($item); // Wajib unset reference setelah loop

        // Jika produk belum ada, tambahkan sebagai item baru
        if (!$product_exists) {
            $rowid = md5($id_produk . microtime());
            $cart[$rowid] = [
                'rowid'   => $rowid,
                'id'      => $id_produk,
                'qty'     => 1,
                'price'   => $harga_asli,
                'name'    => $this->request->getPost('nama'),
                'options' => [
                    'foto'   => $this->request->getPost('foto'),
                    'diskon' => $nilai_diskon,
                ]
            ];
        }

        // Simpan kembali keranjang ke session
        $this->session->set('cart', $cart);

        session()->setflashdata('success', 'Produk berhasil ditambahkan ke keranjang.');
        return redirect()->to(base_url('home'));
    }

    /**
     * Mengosongkan seluruh isi keranjang.
     */
    public function cart_clear()
    {
        $this->session->remove('cart');
        session()->setflashdata('success', 'Keranjang Berhasil Dikosongkan');
        return redirect()->to(base_url('keranjang'));
    }

    /**
     * Mengubah jumlah produk di keranjang.
     */
    public function cart_edit()
    {
        $cart = $this->session->get('cart') ?? [];
        $i = 1;
        
        // Buat keranjang baru untuk menampung perubahan
        $newCart = [];
        foreach ($cart as $rowid => $item) {
            $item['qty'] = (int) $this->request->getPost('qty' . $i++);
            $newCart[$rowid] = $item;
        }

        $this->session->set('cart', $newCart);

        session()->setflashdata('success', 'Keranjang Berhasil Diedit');
        return redirect()->to(base_url('keranjang'));
    }

    /**
     * Menghapus satu item dari keranjang.
     */
    public function cart_delete($rowid)
    {
        $cart = $this->session->get('cart') ?? [];
        if (isset($cart[$rowid])) {
            unset($cart[$rowid]);
        }
        $this->session->set('cart', $cart);

        session()->setflashdata('success', 'Keranjang Berhasil Dihapus');
        return redirect()->to(base_url('keranjang'));
    }

    /**
     * Menampilkan halaman checkout.
     */
    public function checkout()
    {
        if (!session()->has('isLoggedIn')) {
            return redirect()->to('login');
        }

        $data['items'] = $this->session->get('cart') ?? [];
        $total = 0;
        $total_diskon = 0;

        foreach ($data['items'] as $item) {
            $harga_setelah_diskon = $item['price'] - ($item['options']['diskon'] ?? 0);
            $total += $harga_setelah_diskon * $item['qty'];
            $total_diskon += ($item['options']['diskon'] ?? 0) * $item['qty'];
        }
        
        $data['total'] = $total;
        $data['total_diskon'] = $total_diskon;

        return view('v_checkout', $data);
    }
    
    /**
     * API untuk mencari lokasi (RajaOngkir).
     */
    public function getLocation()
    {
        $search = $this->request->getGet('search');
        $response = $this->client->request('GET', 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=' . $search . '&limit=50', ['headers' => ['accept' => 'application/json', 'key' => $this->apiKey]]);
        $body = json_decode($response->getBody(), true);
        return $this->response->setJSON($body['data']);
    }

    /**
     * API untuk menghitung ongkos kirim (RajaOngkir).
     */
    public function getCost()
    {
        $destination = $this->request->getGet('destination');
        $response = $this->client->request('POST', 'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', ['multipart' => [['name' => 'origin', 'contents' => '64999'], ['name' => 'destination', 'contents' => $destination], ['name' => 'weight', 'contents' => '1000'], ['name' => 'courier', 'contents' => 'jne']], 'headers' => ['accept' => 'application/json', 'key' => $this->apiKey]]);
        $body = json_decode($response->getBody(), true);
        return $this->response->setJSON($body['data']);
    }

    /**
     * Memproses pesanan dan menyimpannya ke database.
     */
    public function buy()
    {
        if ($this->request->getPost()) {
            $cart = $this->session->get('cart') ?? [];

            $dataForm = [
                'username' => $this->request->getPost('username'),
                'total_harga' => $this->request->getPost('total_harga'),
                'alamat' => $this->request->getPost('alamat'),
                'ongkir' => $this->request->getPost('ongkir'),
                'status' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $this->transaction->insert($dataForm);
            $last_insert_id = $this->transaction->getInsertID();

            foreach ($cart as $value) {
                $diskon_per_item = $value['options']['diskon'] ?? 0;
                $dataFormDetail = [
                    'transaction_id' => $last_insert_id,
                    'product_id' => $value['id'],
                    'jumlah' => $value['qty'],
                    'diskon' => $diskon_per_item,
                    'subtotal_harga' => ($value['price'] - $diskon_per_item) * $value['qty'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];
                $this->transaction_detail->insert($dataFormDetail);
            }

            // Hapus keranjang dari session
            $this->session->remove('cart');
            
            session()->setFlashdata('success', 'Pesanan Anda berhasil dibuat!');
            return redirect()->to(base_url('home'));
        }
    }
}
