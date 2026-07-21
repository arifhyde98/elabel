<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoansTable extends Migration
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
            'bpkb_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'requester_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'requested_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'approved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Menunggu',
            ],
            'note' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->addKey('bpkb_id');
        $this->forge->addKey('requester_id');
        $this->forge->addKey('approved_by');
        $this->forge->addForeignKey('bpkb_id', 'bpkb', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('requester_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('loans', true);
    }

    public function down()
    {
        $this->forge->dropTable('loans', true);
    }
}
