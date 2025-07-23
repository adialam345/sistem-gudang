<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStokToBarang extends Migration
{
    public function up()
    {
        $this->forge->addColumn('barang', [
            'stok' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'satuan'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('barang', 'stok');
    }
} 