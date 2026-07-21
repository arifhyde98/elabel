<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

class BpkbMergeIdentityDuplicates extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'bpkb:merge-identity-duplicates';
    protected $description = 'Menyatukan duplikat BPKB berdasarkan identitas kendaraan dan menyisakan record prioritas terbaik.';

    public function run(array $params)
    {
        $db = db_connect();

        $groups = $db->query(
            "SELECT
                plate_number,
                COALESCE(no_bpkb, '') AS no_bpkb_key,
                COALESCE(no_rangka, '') AS no_rangka_key,
                COALESCE(no_mesin, '') AS no_mesin_key,
                COUNT(*) AS total
            FROM bpkb
            GROUP BY plate_number, COALESCE(no_bpkb, ''), COALESCE(no_rangka, ''), COALESCE(no_mesin, '')
            HAVING COUNT(*) > 1
            ORDER BY total DESC, plate_number ASC"
        )->getResultArray();

        if ($groups === []) {
            CLI::write('Tidak ada duplikat identitas yang perlu dibersihkan.', 'green');
            return;
        }

        $deleteMap = [];
        foreach ($groups as $group) {
            $rows = $db->query(
                "SELECT id, plate_number, no_bpkb, no_rangka, no_mesin, merek, pengguna, year, vehicle_type, status, box_id, pdf_path, updated_at
                FROM bpkb
                WHERE plate_number = ?
                  AND COALESCE(no_bpkb, '') = ?
                  AND COALESCE(no_rangka, '') = ?
                  AND COALESCE(no_mesin, '') = ?",
                [
                    $group['plate_number'],
                    $group['no_bpkb_key'],
                    $group['no_rangka_key'],
                    $group['no_mesin_key'],
                ]
            )->getResultArray();

            if (count($rows) <= 1) {
                continue;
            }

            usort($rows, [$this, 'comparePriority']);
            $keepRow = $rows[0];

            foreach (array_slice($rows, 1) as $row) {
                $deleteMap[] = [
                    'delete_id' => (int) $row['id'],
                    'keep_id'   => (int) $keepRow['id'],
                ];
            }
        }

        if ($deleteMap === []) {
            CLI::write('Tidak ada record yang perlu digabung.', 'yellow');
            return;
        }

        $deleteIds = array_values(array_unique(array_map(static fn ($item) => (int) $item['delete_id'], $deleteMap)));
        sort($deleteIds);

        $backupTable = 'bpkb_identity_duplicate_backup_' . date('Ymd_His');
        $mappingTable = 'bpkb_identity_duplicate_map_' . date('Ymd_His');

        $db->transException(true);

        try {
            $db->query("CREATE TABLE `{$backupTable}` LIKE `bpkb`");
            $db->query("CREATE TABLE `{$mappingTable}` (`delete_id` INT NOT NULL, `keep_id` INT NOT NULL, PRIMARY KEY (`delete_id`))");

            foreach (array_chunk($deleteIds, 500) as $chunk) {
                $idList = implode(',', array_map('intval', $chunk));
                $db->query("INSERT INTO `{$backupTable}` SELECT * FROM `bpkb` WHERE `id` IN ({$idList})");
            }

            foreach (array_chunk($deleteMap, 500) as $chunk) {
                $values = [];
                foreach ($chunk as $map) {
                    $values[] = '(' . (int) $map['delete_id'] . ', ' . (int) $map['keep_id'] . ')';
                }
                if ($values !== []) {
                    $db->query("INSERT INTO `{$mappingTable}` (`delete_id`, `keep_id`) VALUES " . implode(', ', $values));
                }
            }

            $db->transStart();

            foreach ($deleteMap as $map) {
                $db->table('loans')
                    ->where('bpkb_id', (int) $map['delete_id'])
                    ->set('bpkb_id', (int) $map['keep_id'])
                    ->update();

                $db->table('bpkb_deletes')
                    ->where('bpkb_id', (int) $map['delete_id'])
                    ->set('bpkb_id', (int) $map['keep_id'])
                    ->update();
            }

            foreach (array_chunk($deleteIds, 500) as $chunk) {
                $db->table('bpkb')->whereIn('id', $chunk)->delete();
            }

            $db->transComplete();

            if (! $db->transStatus()) {
                throw new \RuntimeException('Transaksi merge duplikat gagal disimpan.');
            }

            CLI::write('Backup tabel: ' . $backupTable, 'yellow');
            CLI::write('Tabel mapping: ' . $mappingTable, 'yellow');
            CLI::write('Grup identitas duplikat: ' . count($groups), 'yellow');
            CLI::write('Baris yang dihapus: ' . count($deleteIds), 'green');
        } catch (Throwable $e) {
            CLI::error('Gagal merge duplikat identitas: ' . $e->getMessage());
        }
    }

    private function comparePriority(array $left, array $right): int
    {
        $leftScore = $this->priorityScore($left);
        $rightScore = $this->priorityScore($right);

        if ($leftScore !== $rightScore) {
            return $rightScore <=> $leftScore;
        }

        $leftUpdated = strtotime((string) ($left['updated_at'] ?? '')) ?: 0;
        $rightUpdated = strtotime((string) ($right['updated_at'] ?? '')) ?: 0;
        if ($leftUpdated !== $rightUpdated) {
            return $rightUpdated <=> $leftUpdated;
        }

        return ((int) ($right['id'] ?? 0)) <=> ((int) ($left['id'] ?? 0));
    }

    private function priorityScore(array $row): int
    {
        $score = 0;

        if (! empty($row['pdf_path'])) {
            $score += 100;
        }

        $status = (string) ($row['status'] ?? '');
        if ($status === 'Dipinjam') {
            $score += 50;
        } elseif ($status === 'Tersedia') {
            $score += 25;
        }

        foreach (['no_bpkb', 'no_rangka', 'no_mesin'] as $field) {
            $value = strtoupper(trim((string) ($row[$field] ?? '')));
            if ($value !== '' && $value !== '-' && $value !== '0') {
                $score += 5;
            }
        }

        foreach (['merek', 'pengguna'] as $field) {
            $value = trim((string) ($row[$field] ?? ''));
            if ($value !== '') {
                $score += 2;
            }
        }

        if ((int) ($row['year'] ?? 0) > 0) {
            $score += 1;
        }

        return $score;
    }
}
