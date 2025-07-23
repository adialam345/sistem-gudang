<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DummyBarangSeeder extends Seeder
{
    public function run()
    {
        $categories = [1, 2, 3, 4, 5, 6]; // ID kategori yang tersedia
        $satuan = ['Pcs', 'Box', 'Unit', 'Kg', 'Meter', 'Lusin', 'Pack', 'Roll'];
        $prefixes = ['BRG', 'ITM', 'PRD', 'GDS'];
        
        // List nama barang untuk variasi
        $namaBarang = [
            'Kertas HVS',
            'Pulpen',
            'Spidol',
            'Pensil',
            'Penghapus',
            'Penggaris',
            'Map',
            'Amplop',
            'Buku Tulis',
            'Stapler',
            'Isi Staples',
            'Paper Clip',
            'Binder Clip',
            'Tinta Printer',
            'Cartridge',
            'Flashdisk',
            'Hard Disk',
            'Mouse',
            'Keyboard',
            'Monitor',
            'Printer',
            'Scanner',
            'UPS',
            'Kabel LAN',
            'Switch Hub'
        ];

        // Generate 50 dummy records
        for ($i = 1; $i <= 50; $i++) {
            $prefix = $prefixes[array_rand($prefixes)];
            $kode = sprintf("%s-%03d", $prefix, $i);
            
            // Get random nama barang and add variation
            $baseNama = $namaBarang[array_rand($namaBarang)];
            $variation = rand(1, 100);
            $nama = $baseNama . ' ' . $variation;

            $data = [
                'kode' => $kode,
                'nama' => $nama,
                'kategori_id' => $categories[array_rand($categories)],
                'satuan' => $satuan[array_rand($satuan)],
                'stok' => rand(0, 1000),
                'deskripsi' => 'Deskripsi untuk ' . $nama,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Insert data
            $this->db->table('barang')->insert($data);
        }
    }
} 