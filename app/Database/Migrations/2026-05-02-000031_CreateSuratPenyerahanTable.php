<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuratPenyerahanTable extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('surat_penyerahan')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nibar' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'no_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'status_penggunaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'luas' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'tahun' => [
                'type'       => 'YEAR',
                'null'       => true,
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'pemberi_hibah' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('no_surat');
        $this->forge->addKey('nibar');
        $this->forge->createTable('surat_penyerahan');
    }

    public function down(): void
    {
        $this->forge->dropTable('surat_penyerahan', true);
    }
}
