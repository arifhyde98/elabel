<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBpkbBrandAndUserFields extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('merek', 'bpkb')) {
            $this->forge->addColumn('bpkb', [
                'merek' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'no_mesin',
                ],
            ]);
        }

        if (! $this->db->fieldExists('pengguna', 'bpkb')) {
            $this->forge->addColumn('bpkb', [
                'pengguna' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'merek',
                ],
            ]);
        }

        if (! $this->db->fieldExists('merek', 'bpkb_deletes')) {
            $this->forge->addColumn('bpkb_deletes', [
                'merek' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'no_mesin',
                ],
            ]);
        }

        if (! $this->db->fieldExists('pengguna', 'bpkb_deletes')) {
            $this->forge->addColumn('bpkb_deletes', [
                'pengguna' => [
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
