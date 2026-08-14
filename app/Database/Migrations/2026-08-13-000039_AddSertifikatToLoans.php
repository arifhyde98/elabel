<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSertifikatToLoans extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('loans')) {
            return;
        }

        if ($this->db->fieldExists('bpkb_id', 'loans')) {
            $this->forge->modifyColumn('loans', [
                'bpkb_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]);
        }

        if (! $this->db->fieldExists('sertifikat_id', 'loans')) {
            $this->forge->addColumn('loans', [
                'sertifikat_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'bpkb_id',
                ],
            ]);
            $this->forge->addKey('sertifikat_id');
            $this->forge->addForeignKey('sertifikat_id', 'sertifikat_tanah', 'id', 'CASCADE', 'RESTRICT');
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('loans')) {
            return;
        }

        if ($this->db->fieldExists('sertifikat_id', 'loans')) {
            $this->forge->dropForeignKey('loans', 'loans_sertifikat_id_foreign');
            $this->forge->dropColumn('loans', 'sertifikat_id');
        }

        if ($this->db->fieldExists('bpkb_id', 'loans')) {
            $this->forge->modifyColumn('loans', [
                'bpkb_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => false,
                ],
            ]);
        }
    }
}
