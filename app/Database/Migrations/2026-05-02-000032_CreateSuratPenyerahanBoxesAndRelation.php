<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuratPenyerahanBoxesAndRelation extends Migration
{
    public function up(): void
    {
        $db = $this->db;

        if (! $db->tableExists('surat_penyerahan_boxes')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'box_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                ],
                'lokasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
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
            $this->forge->addUniqueKey('box_code', 'surat_penyerahan_boxes_code_unique');
            $this->forge->createTable('surat_penyerahan_boxes', true);
        }

        if ($db->tableExists('surat_penyerahan') && ! $db->fieldExists('box_id', 'surat_penyerahan')) {
            $this->forge->addColumn('surat_penyerahan', [
                'box_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'pemberi_hibah',
                ],
            ]);
        }

        if (! $db->tableExists('surat_penyerahan')) {
            return;
        }

        $existingRows = $db->table('surat_penyerahan')
            ->select('id, lokasi')
            ->orderBy('id', 'asc')
            ->get()
            ->getResultArray();

        if ($existingRows === []) {
            return;
        }

        $boxMap = [];
        $boxCounts = [];
        $existingBoxes = $db->table('surat_penyerahan_boxes')->orderBy('id', 'asc')->get()->getResultArray();
        foreach ($existingBoxes as $box) {
            $lokasi = trim((string) ($box['lokasi'] ?? ''));
            if ($lokasi === '') {
                continue;
            }
            $boxMap[$lokasi][] = [
                'id' => (int) $box['id'],
                'box_code' => (string) $box['box_code'],
            ];
        }

        $countRows = $db->table('surat_penyerahan')
            ->select('box_id, COUNT(id) AS total')
            ->where('box_id IS NOT NULL', null, false)
            ->groupBy('box_id')
            ->get()
            ->getResultArray();
        foreach ($countRows as $row) {
            $boxCounts[(int) $row['box_id']] = (int) ($row['total'] ?? 0);
        }

        $nextNumber = 0;
        foreach ($existingBoxes as $box) {
            if (preg_match('/^SP-(\d+)$/', (string) ($box['box_code'] ?? ''), $matches) === 1) {
                $nextNumber = max($nextNumber, (int) $matches[1]);
            }
        }

        foreach ($existingRows as $row) {
            $id = (int) ($row['id'] ?? 0);
            $lokasi = trim((string) ($row['lokasi'] ?? ''));
            if ($id <= 0 || $lokasi === '') {
                continue;
            }

            $targetBoxId = null;
            foreach ($boxMap[$lokasi] ?? [] as $box) {
                $boxId = (int) ($box['id'] ?? 0);
                $currentCount = $boxCounts[$boxId] ?? 0;
                if ($currentCount < 40) {
                    $targetBoxId = $boxId;
                    break;
                }
            }

            if ($targetBoxId === null) {
                $nextNumber++;
                $boxCode = 'SP-' . str_pad((string) $nextNumber, 2, '0', STR_PAD_LEFT);
                $db->table('surat_penyerahan_boxes')->insert([
                    'box_code' => $boxCode,
                    'lokasi' => $lokasi,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $targetBoxId = (int) $db->insertID();
                $boxMap[$lokasi][] = [
                    'id' => $targetBoxId,
                    'box_code' => $boxCode,
                ];
                $boxCounts[$targetBoxId] = 0;
            }

            $db->table('surat_penyerahan')
                ->where('id', $id)
                ->update(['box_id' => $targetBoxId]);
            $boxCounts[$targetBoxId] = ($boxCounts[$targetBoxId] ?? 0) + 1;
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('surat_penyerahan') && $this->db->fieldExists('box_id', 'surat_penyerahan')) {
            $this->forge->dropColumn('surat_penyerahan', 'box_id');
        }

        $this->forge->dropTable('surat_penyerahan_boxes', true);
    }
}
