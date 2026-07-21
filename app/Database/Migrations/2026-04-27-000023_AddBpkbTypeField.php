<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBpkbTypeField extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('tipe', 'bpkb')) {
            $this->forge->addColumn('bpkb', [
                'tipe' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'merek',
                ],
            ]);
        }

        if (! $this->db->fieldExists('tipe', 'bpkb_deletes')) {
            $this->forge->addColumn('bpkb_deletes', [
                'tipe' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'merek',
                ],
            ]);
        }
    }

    public function down()
    {
        // Kolom dipertahankan untuk menjaga data.
    }
}
