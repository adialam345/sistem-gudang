<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdatePriceSeeder extends Seeder
{
    public function run()
    {
        // Update harga di tabel barang_masuk
        $this->db->query("UPDATE barang_masuk bm 
                         JOIN barang b ON bm.barang_id = b.id 
                         SET bm.harga = b.harga 
                         WHERE bm.harga = 0");

        // Update harga di tabel barang_keluar
        $this->db->query("UPDATE barang_keluar bk 
                         JOIN barang b ON bk.barang_id = b.id 
                         SET bk.harga = b.harga 
                         WHERE bk.harga = 0");
    }
} 