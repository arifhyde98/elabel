<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBpkbCylinderAndColorFields extends Migration
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
        if (! $this->db->fieldExists('isi_silinder', $table)) {
            $this->forge->addColumn($table, [
                'isi_silinder' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'tipe',
                ],
            ]);
        }

        if (! $this->db->fieldExists('warna', $table)) {
            $this->forge->addColumn($table, [
                'warna' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'isi_silinder',
                ],
            ]);
        }
    }
}
