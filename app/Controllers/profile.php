<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use App\Models\ProdukModel;
use App\Models\DiskonModel; // 1. Panggil DiskonModel

class profile extends BaseController
{
    public function index()
    {
        helper(['number', 'form']);

        $transactionModel = new TransactionModel();
        $transactionDetailModel = new TransactionDetailModel();
        $diskonModel = new DiskonModel(); // 2. Inisialisasi DiskonModel
        $username = session()->get('username');
        $tanggal_hari_ini = date('Y-m-d');

        // Ambil data transaksi utama
        $transactions = $transactionModel->where('username', $username)
                                         ->orderBy('created_at', 'DESC')
                                         ->findAll();

        $data_product = [];
        $data_diskon_live = []; // 3. Siapkan array untuk diskon live

        foreach ($transactions as $transaction) {
            $transaction_id = $transaction['id'];

            // Ambil detail produk seperti biasa
            $product_details = $transactionDetailModel
                ->select('transaction_detail.*, product.nama as nama_produk, product.harga as harga_produk, product.foto as foto_produk')
                ->join('product', 'product.id = transaction_detail.product_id')
                ->where('transaction_id', $transaction_id)
                ->findAll();

            // 4. Untuk setiap produk, cek diskon hari ini
            foreach ($product_details as $detail) {
                $diskon_hari_ini = $diskonModel
                    ->where('id_produk', $detail['product_id'])
                    ->where('tanggal', $tanggal_hari_ini)
                    ->first();

                // Simpan nilai diskon live
                $data_diskon_live[$detail['id']] = $diskon_hari_ini['nominal'] ?? 0;
            }

            $data_product[$transaction_id] = $product_details;
        }

        $data = [
            'transactions' => $transactions,
            'products' => $data_product,
            'username' => $username,
            'diskon_live' => $data_diskon_live, // 5. Kirim data diskon live ke view
        ];

        return view('v_profile', $data);
    }
}
