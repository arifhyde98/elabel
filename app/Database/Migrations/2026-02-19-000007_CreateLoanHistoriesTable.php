<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoanHistoriesTable extends Migration
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
            'loan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'changed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'changed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'note' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('loan_id');
        $this->forge->addKey('changed_by');
        $this->forge->addForeignKey('loan_id', 'loans', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('loan_histories', true);
    }

    public function down()
    {
        $this->forge->dropTable('loan_histories', true);
    }
}
