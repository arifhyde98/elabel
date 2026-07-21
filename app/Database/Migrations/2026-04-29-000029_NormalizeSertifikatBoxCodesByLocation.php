<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeSertifikatBoxCodesByLocation extends Migration
{
    public function up()
    {
        $boxes = $this->db->table('sertifikat_boxes')
            ->orderBy('lokasi', 'asc')
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

            $grouped[$lokasi][] = $box;
        }

        foreach ($grouped as $items) {
            if (count($items) < 2) {
                continue;
            }

            $baseCode = (string) ($items[0]['box_code'] ?? '');
            if ($baseCode === '') {
                continue;
            }

            foreach ($items as $index => $box) {
                if ($index === 0) {
                    continue;
                }

                $newCode = $baseCode . ' (' . ($index + 1) . ')';
                $this->db->table('sertifikat_boxes')
                    ->where('id', (int) $box['id'])
                    ->update(['box_code' => $newCode]);
            }
        }
    }

    public function down()
    {
        // no-op
    }
}
