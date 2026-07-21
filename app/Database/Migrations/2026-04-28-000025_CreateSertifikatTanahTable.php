<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSertifikatTanahTable extends Migration
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
            'no_sertipikat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nibar' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status_penggunaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'spesifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'luas' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
            ],
            'tanggal_perolehan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'nilai_perolehan' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
            ],
            'nama_pemilik' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'cara_perolehan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'alamat' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'dinas' => [
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
        $this->forge->addKey('no_sertipikat');
        $this->forge->createTable('sertifikat_tanah', true);
    }

    public function down()
    {
        $this->forge->dropTable('sertifikat_tanah', true);
    }
}
