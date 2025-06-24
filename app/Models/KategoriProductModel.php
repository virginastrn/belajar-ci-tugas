<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriProductModel extends Model
{
    protected $table = 'nama_tabel'; // Ganti sesuai nama tabel kategori kamu
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama',];
    public $useTimestamps = false;
}
