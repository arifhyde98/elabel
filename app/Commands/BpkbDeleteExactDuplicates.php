<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

class BpkbDeleteExactDuplicates extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'bpkb:delete-exact-duplicates';
    protected $description = 'Membackup lalu menghapus data BPKB yang duplikat persis, dengan menyisakan ID terkecil.';

    public function run(array $params)
    {
        $db = db_connect();
        $comparableFields = [
            'box_id',
            'year',
            'vehicle_type',
            'plate_number',
            'no_bpkb',
            'no_rangka',
            'no_mesin',
            'merek',
            'pengguna',
            'status',
            'pdf_path',
            'input_by',
        ];

        $groupBy = implode(', ', $comparableFields);
        $duplicateGroups = $db->query(
            "SELECT MIN(id) AS keep_id, GROUP_CONCAT(id ORDER BY id ASC) AS grouped_ids, COUNT(*) AS total
            FROM bpkb
            GROUP BY {$groupBy}
            HAVING COUNT(*) > 1
            ORDER BY keep_id ASC"
        )->getResultArray();

        if ($duplicateGroups === []) {
            CLI::write('Tidak ada duplikat persis yang aman untuk dihapus.', 'green');
            return;
        }

        $deleteIds = [];
        foreach ($duplicateGroups as $group) {
            $ids = array_values(array_filter(array_map('intval', explode(',', (string) ($group['grouped_ids'] ?? '')))));
            if (count($ids) <= 1) {
                continue;
            }

            $keepId = (int) ($group['keep_id'] ?? 0);
            foreach ($ids as $id) {
                if ($id !== $keepId) {
                    $deleteIds[] = $id;
                }
            }
        }

        $deleteIds = array_values(array_unique($deleteIds));
        sort($deleteIds);

        if ($deleteIds === []) {
            CLI::write('Duplikat terdeteksi, tetapi tidak ada ID yang perlu dihapus.', 'yellow');
            return;
        }

        $backupTable = 'bpkb_duplicate_backup_' . date('Ymd_His');
        $db->transException(true);

        try {
            $db->query("CREATE TABLE `{$backupTable}` LIKE `bpkb`");

            foreach (array_chunk($deleteIds, 500) as $chunk) {
                $idList = implode(',', array_map('intval', $chunk));
                $db->query("INSERT INTO `{$backupTable}` SELECT * FROM `bpkb` WHERE `id` IN ({$idList})");
            }

            $db->transStart();
            foreach (array_chunk($deleteIds, 500) as $chunk) {
                $db->table('bpkb')->whereIn('id', $chunk)->delete();
            }
            $db->transComplete();

            if (! $db->transStatus()) {
                throw new \RuntimeException('Transaksi penghapusan gagal disimpan.');
            }

            CLI::write('Backup tabel: ' . $backupTable, 'yellow');
            CLI::write('Grup duplikat persis: ' . count($duplicateGroups), 'yellow');
            CLI::write('Baris yang dihapus: ' . count($deleteIds), 'green');
        } catch (Throwable $e) {
            CLI::error('Gagal menghapus duplikat: ' . $e->getMessage());
        }
    }
}
