<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPriceToTransactions extends Migration
{
    public function up()
    {
        // Add harga column to barang_masuk table
        $this->forge->addColumn('barang_masuk', [
            'harga' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => 0.00,
                'after' => 'satuan'
            ]
        ]);

        // Add harga column to barang_keluar table
        $this->forge->addColumn('barang_keluar', [
            'harga' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => 0.00,
                'after' => 'satuan'
            ]
        ]);
    }

    public function down()
    {
        // Remove harga column from barang_masuk table
        $this->forge->dropColumn('barang_masuk', 'harga');
        
        // Remove harga column from barang_keluar table
        $this->forge->dropColumn('barang_keluar', 'harga');
    }
} 