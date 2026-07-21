<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BpkbAuditDuplicates extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'bpkb:audit-duplicates';
    protected $description = 'Menampilkan grup data BPKB yang terduplikasi berdasarkan field identitas.';

    public function run(array $params)
    {
        $db = db_connect();
        $fields = [
            'plate_number' => 'No. Polisi',
            'no_bpkb'      => 'No. BPKB',
            'no_rangka'    => 'No. Rangka',
            'no_mesin'     => 'No. Mesin',
        ];

        foreach ($fields as $field => $label) {
            CLI::newLine();
            CLI::write('=== ' . $label . ' (' . $field . ') ===', 'yellow');

            $duplicateGroups = $db->query(
                "SELECT {$field} AS identity_value, COUNT(*) AS total
                FROM bpkb
                WHERE {$field} IS NOT NULL
                  AND TRIM({$field}) <> ''
                  AND {$field} <> '-'
                  AND {$field} <> '0'
                GROUP BY {$field}
                HAVING COUNT(*) > 1
                ORDER BY total DESC, {$field} ASC"
            )->getResultArray();

            if ($duplicateGroups === []) {
                CLI::write('Tidak ada duplikat.', 'green');
                continue;
            }

            foreach ($duplicateGroups as $group) {
                $value = (string) ($group['identity_value'] ?? '');
                $total = (int) ($group['total'] ?? 0);
                CLI::write($value . ' -> ' . $total . ' data', 'light_red');

                $rows = $db->table('bpkb')
                    ->select('id, plate_number, no_bpkb, no_rangka, no_mesin, year, vehicle_type, status')
                    ->where($field, $value)
                    ->orderBy('id', 'asc')
                    ->get()
                    ->getResultArray();

                foreach ($rows as $row) {
                    CLI::write(
                        sprintf(
                            '  ID %d | %s | BPKB: %s | Rangka: %s | Mesin: %s | Tahun: %s | Jenis: %s | Status: %s',
                            (int) ($row['id'] ?? 0),
                            (string) ($row['plate_number'] ?? '-'),
                            (string) ($row['no_bpkb'] ?? '-'),
                            (string) ($row['no_rangka'] ?? '-'),
                            (string) ($row['no_mesin'] ?? '-'),
                            (string) ($row['year'] ?? '-'),
                            (string) ($row['vehicle_type'] ?? '-'),
                            (string) ($row['status'] ?? '-')
                        )
                    );
                }
            }
        }
    }
}
