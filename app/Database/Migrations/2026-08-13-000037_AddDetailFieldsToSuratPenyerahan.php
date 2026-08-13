<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailFieldsToSuratPenyerahan extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('surat_penyerahan')) {
            return;
        }

        $fields = [];

        if (! $this->db->fieldExists('spesifikasi', 'surat_penyerahan')) {
            $fields['spesifikasi'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status_penggunaan',
            ];
        }

        if (! $this->db->fieldExists('jenis_penyerahan', 'surat_penyerahan')) {
            $fields['jenis_penyerahan'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'spesifikasi',
            ];
        }

        if (! $this->db->fieldExists('alamat', 'surat_penyerahan')) {
            $fields['alamat'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'tahun',
            ];
        }

        if (! $this->db->fieldExists('dinas', 'surat_penyerahan')) {
            $fields['dinas'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'lokasi',
            ];
        }

        if (! $this->db->fieldExists('pdf_path', 'surat_penyerahan')) {
            $fields['pdf_path'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'pemberi_hibah',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('surat_penyerahan', $fields);
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('surat_penyerahan')) {
            return;
        }

        foreach (['pdf_path', 'dinas', 'alamat', 'jenis_penyerahan', 'spesifikasi'] as $field) {
            if ($this->db->fieldExists($field, 'surat_penyerahan')) {
                $this->forge->dropColumn('surat_penyerahan', $field);
            }
        }
    }
}
