<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBoxYearsTable extends Migration
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
            'year' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('box_id');
        $this->forge->addKey('year');
        $this->forge->addUniqueKey(['box_id', 'year'], 'box_year_unique');
        $this->forge->addForeignKey('box_id', 'boxes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('box_years', true);
    }

    public function down()
    {
        $this->forge->dropTable('box_years', true);
    }
}
