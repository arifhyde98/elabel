<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBpkbIdentityUniqueIndex extends Migration
{
    public function up()
    {
        $db = $this->db;
        $database = $db->getDatabase();

        $hasPlateKey = $db->query(
            "SELECT COUNT(*) AS total
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'bpkb'
              AND COLUMN_NAME = 'plate_number_key'",
            [$database]
        )->getRowArray();

        if ((int) ($hasPlateKey['total'] ?? 0) === 0) {
            $db->query(
                "ALTER TABLE `bpkb`
                ADD COLUMN `plate_number_key` VARCHAR(20)
                    GENERATED ALWAYS AS (UPPER(TRIM(`plate_number`))) STORED,
                ADD COLUMN `no_bpkb_key` VARCHAR(50)
                    GENERATED ALWAYS AS (
                        CASE
                            WHEN `no_bpkb` IS NULL OR TRIM(`no_bpkb`) IN ('', '-', '0') THEN ''
                            ELSE UPPER(TRIM(`no_bpkb`))
                        END
                    ) STORED,
                ADD COLUMN `no_rangka_key` VARCHAR(50)
                    GENERATED ALWAYS AS (
                        CASE
                            WHEN `no_rangka` IS NULL OR TRIM(`no_rangka`) IN ('', '-', '0') THEN ''
                            ELSE UPPER(TRIM(`no_rangka`))
                        END
                    ) STORED,
                ADD COLUMN `no_mesin_key` VARCHAR(50)
                    GENERATED ALWAYS AS (
                        CASE
                            WHEN `no_mesin` IS NULL OR TRIM(`no_mesin`) IN ('', '-', '0') THEN ''
                            ELSE UPPER(TRIM(`no_mesin`))
                        END
                    ) STORED"
            );
        }

        $hasIndex = $db->query(
            "SELECT COUNT(*) AS total
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'bpkb'
              AND INDEX_NAME = 'bpkb_identity_unique'",
            [$database]
        )->getRowArray();

        if ((int) ($hasIndex['total'] ?? 0) === 0) {
            $db->query(
                "CREATE UNIQUE INDEX `bpkb_identity_unique`
                ON `bpkb` (`plate_number_key`, `no_bpkb_key`, `no_rangka_key`, `no_mesin_key`)"
            );
        }
    }

    public function down()
    {
        $db = $this->db;
        $database = $db->getDatabase();

        $hasIndex = $db->query(
            "SELECT COUNT(*) AS total
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'bpkb'
              AND INDEX_NAME = 'bpkb_identity_unique'",
            [$database]
        )->getRowArray();

        if ((int) ($hasIndex['total'] ?? 0) > 0) {
            $db->query("DROP INDEX `bpkb_identity_unique` ON `bpkb`");
        }

        $columns = ['plate_number_key', 'no_bpkb_key', 'no_rangka_key', 'no_mesin_key'];
        foreach ($columns as $column) {
            $exists = $db->query(
                "SELECT COUNT(*) AS total
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = 'bpkb'
                  AND COLUMN_NAME = ?",
                [$database, $column]
            )->getRowArray();

            if ((int) ($exists['total'] ?? 0) > 0) {
                $db->query("ALTER TABLE `bpkb` DROP COLUMN `{$column}`");
            }
        }
    }
}
