<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSertifikatUniqueIndexes extends Migration
{
    public function up()
    {
        $db = $this->db;
        $database = $db->getDatabase();

        $columns = [
            'no_sertipikat_key' => "CASE WHEN `no_sertipikat` IS NULL OR TRIM(`no_sertipikat`) = '' THEN NULL ELSE UPPER(TRIM(`no_sertipikat`)) END",
            'nibar_key' => "CASE WHEN `nibar` IS NULL OR TRIM(`nibar`) = '' THEN NULL ELSE UPPER(TRIM(`nibar`)) END",
        ];

        foreach ($columns as $column => $expression) {
            $exists = $db->query(
                "SELECT COUNT(*) AS total
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = 'sertifikat_tanah'
                  AND COLUMN_NAME = ?",
                [$database, $column]
            )->getRowArray();

            if ((int) ($exists['total'] ?? 0) === 0) {
                $db->query(
                    "ALTER TABLE `sertifikat_tanah`
                    ADD COLUMN `{$column}` VARCHAR(100)
                    GENERATED ALWAYS AS ({$expression}) STORED"
                );
            }
        }

        $indexes = [
            'sertifikat_no_sertipikat_unique' => 'no_sertipikat_key',
            'sertifikat_nibar_unique' => 'nibar_key',
        ];

        foreach ($indexes as $indexName => $column) {
            $exists = $db->query(
                "SELECT COUNT(*) AS total
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = 'sertifikat_tanah'
                  AND INDEX_NAME = ?",
                [$database, $indexName]
            )->getRowArray();

            if ((int) ($exists['total'] ?? 0) === 0) {
                $db->query(
                    "CREATE UNIQUE INDEX `{$indexName}` ON `sertifikat_tanah` (`{$column}`)"
                );
            }
        }
    }

    public function down()
    {
        $db = $this->db;
        $database = $db->getDatabase();

        $indexes = ['sertifikat_no_sertipikat_unique', 'sertifikat_nibar_unique'];
        foreach ($indexes as $indexName) {
            $exists = $db->query(
                "SELECT COUNT(*) AS total
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = 'sertifikat_tanah'
                  AND INDEX_NAME = ?",
                [$database, $indexName]
            )->getRowArray();

            if ((int) ($exists['total'] ?? 0) > 0) {
                $db->query("DROP INDEX `{$indexName}` ON `sertifikat_tanah`");
            }
        }

        foreach (['no_sertipikat_key', 'nibar_key'] as $column) {
            $exists = $db->query(
                "SELECT COUNT(*) AS total
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = 'sertifikat_tanah'
                  AND COLUMN_NAME = ?",
                [$database, $column]
            )->getRowArray();

            if ((int) ($exists['total'] ?? 0) > 0) {
                $db->query("ALTER TABLE `sertifikat_tanah` DROP COLUMN `{$column}`");
            }
        }
    }
}
