<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class DiskonSeeder extends Seeder
{
    public function run()
    {
        // Contoh nominal diskon
        $nominal_values = [100000, 200000, 50000, 150000, 75000];
        
        // Loop untuk membuat 10 data
        for ($i = 0; $i < 10; $i++) {
            $data = [
                // Mengisi tanggal mulai dari hari ini hingga 9 hari ke depan
                'tanggal' => Time::today()->addDays($i)->toDateString(),
                // Memilih nominal secara acak dari contoh di atas
                'nominal'    => $nominal_values[array_rand($nominal_values)],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            // Menggunakan Query Builder untuk memasukkan data
            $this->db->table('diskon')->insert($data);
        }
    }
}