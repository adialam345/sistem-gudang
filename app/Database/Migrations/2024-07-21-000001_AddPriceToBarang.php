<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPriceToBarang extends Migration
{
    public function up()
    {
        $this->forge->addColumn('barang', [
            'harga' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => 0.00,
                'after' => 'deskripsi'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('barang', 'harga');
    }
} 