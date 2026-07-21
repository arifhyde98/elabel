<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBoxesForMultiYear extends Migration
{
    public function up()
    {
        $fields = [
            'box_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('boxes', $fields);

        // Drop unique key on year and remove year column to allow multi-year in a box.
        $this->forge->dropKey('boxes', 'boxes_year_unique', false);
        $this->forge->dropColumn('boxes', 'year');

        $this->forge->addUniqueKey('box_code', 'boxes_code_unique');
    }

    public function down()
    {
        $this->forge->dropKey('boxes', 'boxes_code_unique', false);
        $this->forge->addColumn('boxes', [
            'year' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
            ],
        ]);
        $this->forge->addUniqueKey('year', 'boxes_year_unique');

        $this->forge->dropColumn('boxes', ['box_code', 'location']);
    }
}
