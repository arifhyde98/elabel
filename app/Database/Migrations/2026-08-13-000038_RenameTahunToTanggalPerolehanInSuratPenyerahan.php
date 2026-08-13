<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTahunToTanggalPerolehanInSuratPenyerahan extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('surat_penyerahan')) {
            return;
        }

        if (! $this->db->fieldExists('tanggal_perolehan', 'surat_penyerahan')) {
            $this->forge->addColumn('surat_penyerahan', [
                'tanggal_perolehan' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'luas',
                ],
            ]);
        }

        if ($this->db->fieldExists('tahun', 'surat_penyerahan')) {
            $this->db->query("UPDATE surat_penyerahan SET tanggal_perolehan = CONCAT(tahun, '-01-01') WHERE tanggal_perolehan IS NULL AND tahun IS NOT NULL");
            $this->forge->dropColumn('surat_penyerahan', 'tahun');
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('surat_penyerahan')) {
            return;
        }

        if (! $this->db->fieldExists('tahun', 'surat_penyerahan')) {
            $this->forge->addColumn('surat_penyerahan', [
                'tahun' => [
                    'type' => 'YEAR',
                    'null' => true,
                    'after' => 'luas',
                ],
            ]);
        }

        if ($this->db->fieldExists('tanggal_perolehan', 'surat_penyerahan')) {
            $this->db->query('UPDATE surat_penyerahan SET tahun = YEAR(tanggal_perolehan) WHERE tahun IS NULL AND tanggal_perolehan IS NOT NULL');
            $this->forge->dropColumn('surat_penyerahan', 'tanggal_perolehan');
        }
    }
}
