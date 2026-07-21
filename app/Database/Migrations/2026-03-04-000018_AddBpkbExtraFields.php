<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBpkbExtraFields extends Migration
{
    public function up()
    {
        $fields = [
            'no_bpkb' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'plate_number',
            ],
            'no_rangka' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'no_bpkb',
            ],
            'no_mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'no_rangka',
            ],
        ];

        foreach ($fields as $name => $def) {
            if (! $this->db->fieldExists($name, 'bpkb')) {
                $this->forge->addColumn('bpkb', [$name => $def]);
            }
        }
    }

    public function down()
    {
        // keep columns
    }
}
