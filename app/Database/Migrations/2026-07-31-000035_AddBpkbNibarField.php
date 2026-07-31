<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBpkbNibarField extends Migration
{
    public function up()
    {
        $this->addFields('bpkb');
        $this->addFields('bpkb_deletes');
    }

    public function down()
    {
        // Kolom dipertahankan untuk menjaga data.
    }

    private function addFields(string $table): void
    {
        if (! $this->db->fieldExists('nibar', $table)) {
            $this->forge->addColumn($table, [
                'nibar' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'no_bpkb',
                ],
            ]);
        }
    }
}
