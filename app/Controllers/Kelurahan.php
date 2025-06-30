<?php

namespace App\Controllers;

use App\Models\KelurahanModel;
use CodeIgniter\Controller;

class Kelurahan extends Controller
{
    public function search()
    {
        $term = $this->request->getGet('term');
        $model = new KelurahanModel();

        $data = $model->like('nama', $term)->findAll();

        $results = [];
        foreach ($data as $row) {
            $results[] = [
                'id' => $row['id'],      // sesuaikan dengan nama kolom id
                'text' => $row['nama']   // sesuaikan dengan nama kolom nama
            ];
        }

        return $this->response->setJSON($results);
    }
}

