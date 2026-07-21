<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLoansForGuestRequests extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('loans', [
            'requester_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->forge->addColumn('loans', [
            'requester_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'requester_id',
            ],
            'requester_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'requester_name',
            ],
            'requester_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'requester_phone',
            ],
            'requester_org' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'requester_email',
            ],
            'requester_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'requester_org',
            ],
            'requester_note' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'requester_address',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('loans', [
            'requester_note',
            'requester_address',
            'requester_org',
            'requester_email',
            'requester_phone',
            'requester_name',
        ]);

        $this->forge->modifyColumn('loans', [
            'requester_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
