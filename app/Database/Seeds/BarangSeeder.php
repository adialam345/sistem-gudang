<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kode' => 'BRG001',
                'nama' => 'Besi Beton 10mm',
                'kategori_id' => 1,
                'satuan' => 'batang',
                'deskripsi' => 'Besi beton diameter 10mm panjang 12 meter',
                'harga' => 75000.00
            ],
            [
                'kode' => 'BRG002',
                'nama' => 'Semen Portland 50kg',
                'kategori_id' => 1,
                'satuan' => 'sak',
                'deskripsi' => 'Semen portland kemasan 50kg',
                'harga' => 85000.00
            ],
            [
                'kode' => 'BRG003',
                'nama' => 'Cat Tembok 25kg',
                'kategori_id' => 1,
                'satuan' => 'pail',
                'deskripsi' => 'Cat tembok interior warna putih kemasan 25kg',
                'harga' => 450000.00
            ],
            [
                'kode' => 'BRG004',
                'nama' => 'Paku 5cm',
                'kategori_id' => 1,
                'satuan' => 'kg',
                'deskripsi' => 'Paku ukuran 5cm',
                'harga' => 25000.00
            ],
            [
                'kode' => 'BRG005',
                'nama' => 'Keramik 60x60',
                'kategori_id' => 1,
                'satuan' => 'dus',
                'deskripsi' => 'Keramik lantai ukuran 60x60cm',
                'harga' => 185000.00
            ]
        ];

        foreach ($data as $row) {
            $this->db->table('barang')->insert($row);
        }
    }
} 