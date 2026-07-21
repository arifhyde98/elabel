<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLoanHistoriesForGuests extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('loan_histories', [
            'changed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('loan_histories', [
            'changed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
