<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdatePriceSeeder extends Seeder
{
    public function run()
    {
        // Update prices in barang table
        $this->db->query("UPDATE barang SET harga = 75000.00 WHERE kode = 'BRG001'");
        $this->db->query("UPDATE barang SET harga = 85000.00 WHERE kode = 'BRG002'");
        $this->db->query("UPDATE barang SET harga = 450000.00 WHERE kode = 'BRG003'");
        $this->db->query("UPDATE barang SET harga = 25000.00 WHERE kode = 'BRG004'");
        $this->db->query("UPDATE barang SET harga = 185000.00 WHERE kode = 'BRG005'");

        // Update prices in barang_masuk table
        $this->db->query("UPDATE barang_masuk bm 
                         JOIN barang b ON bm.barang_id = b.id 
                         SET bm.harga = b.harga 
                         WHERE bm.harga = 0");

        // Update prices in barang_keluar table
        $this->db->query("UPDATE barang_keluar bk 
                         JOIN barang b ON bk.barang_id = b.id 
                         SET bk.harga = b.harga 
                         WHERE bk.harga = 0");
    }
} 