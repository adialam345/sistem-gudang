<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'auto_increment' => true, 'unsigned' => true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'          => ['type' => 'ENUM', 'constraint' => ['admin', 'staff'], 'default' => 'staff'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_by'    => ['type' => 'INT', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_by'    => ['type' => 'INT', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_by'    => ['type' => 'INT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
} 