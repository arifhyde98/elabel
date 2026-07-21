<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBpkbDeletesTable extends Migration
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
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'reason_detail' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('bpkb_id');
        $this->forge->addKey('deleted_by');
        $this->forge->addForeignKey('bpkb_id', 'bpkb', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('deleted_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('bpkb_deletes', true);
    }

    public function down()
    {
        $this->forge->dropTable('bpkb_deletes', true);
    }
}
