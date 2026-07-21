<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeSertifikatBoxCodesCaseInsensitive extends Migration
{
    public function up()
    {
        $boxes = $this->db->table('sertifikat_boxes')
            ->orderBy('id', 'asc')
            ->get()
            ->getResultArray();

        if ($boxes === []) {
            return;
        }

        $grouped = [];
        foreach ($boxes as $box) {
            $lokasi = trim((string) ($box['lokasi'] ?? ''));
            if ($lokasi === '') {
                continue;
            }

            $grouped[strtoupper($lokasi)][] = $box;
        }

        foreach ($grouped as $items) {
            if (count($items) < 2) {
                continue;
            }

            $baseCode = preg_replace('/ \(\d+\)$/', '', (string) ($items[0]['box_code'] ?? ''));
            if (! is_string($baseCode) || $baseCode === '') {
                continue;
            }

            foreach ($items as $index => $box) {
                if ($index === 0) {
                    continue;
                }

                $this->db->table('sertifikat_boxes')
                    ->where('id', (int) $box['id'])
                    ->update([
                        'box_code' => $baseCode . ' (' . ($index + 1) . ')',
                    ]);
            }
        }
    }

    public function down()
    {
        // no-op
    }
}
