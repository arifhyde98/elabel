<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBpkbTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'box_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'plate_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Tersedia',
            ],
            'pdf_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'input_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('box_id');
        $this->forge->addKey('input_by');
        $this->forge->addForeignKey('box_id', 'boxes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('input_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('bpkb', true);
    }

    public function down()
    {
        $this->forge->dropTable('bpkb', true);
    }
}
