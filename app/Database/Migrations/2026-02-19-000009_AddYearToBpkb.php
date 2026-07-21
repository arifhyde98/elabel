<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddYearToBpkb extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bpkb', [
            'year' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
                'after'      => 'box_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bpkb', 'year');
    }
}
