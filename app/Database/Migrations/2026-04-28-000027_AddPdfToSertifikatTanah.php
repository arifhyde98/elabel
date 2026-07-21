<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPdfToSertifikatTanah extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('pdf_path', 'sertifikat_tanah')) {
            $this->forge->addColumn('sertifikat_tanah', [
                'pdf_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'dinas',
                ],
            ]);
        }
    }

    public function down()
    {
        // keep column to preserve uploaded files references
    }
}
