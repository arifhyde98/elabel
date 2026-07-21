<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBpkbUniqueIndexToPlateYear extends Migration
{
    public function up()
    {
        $db = $this->db;
        $database = $db->getDatabase();

        $hasOldIndex = $db->query(
            "SELECT COUNT(*) AS total
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'bpkb'
              AND INDEX_NAME = 'bpkb_identity_unique'",
            [$database]
        )->getRowArray();

        if ((int) ($hasOldIndex['total'] ?? 0) > 0) {
            $db->query("DROP INDEX `bpkb_identity_unique` ON `bpkb`");
        }

        $hasNewIndex = $db->query(
            "SELECT COUNT(*) AS total
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'bpkb'
              AND INDEX_NAME = 'bpkb_plate_year_unique'",
            [$database]
        )->getRowArray();

        if ((int) ($hasNewIndex['total'] ?? 0) === 0) {
            $db->query(
                "CREATE UNIQUE INDEX `bpkb_plate_year_unique`
                ON `bpkb` (`plate_number_key`, `year`)"
            );
        }
    }

    public function down()
    {
        $db = $this->db;
        $database = $db->getDatabase();

        $hasNewIndex = $db->query(
            "SELECT COUNT(*) AS total
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'bpkb'
              AND INDEX_NAME = 'bpkb_plate_year_unique'",
            [$database]
        )->getRowArray();

        if ((int) ($hasNewIndex['total'] ?? 0) > 0) {
            $db->query("DROP INDEX `bpkb_plate_year_unique` ON `bpkb`");
        }

        $hasOldIndex = $db->query(
            "SELECT COUNT(*) AS total
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'bpkb'
              AND INDEX_NAME = 'bpkb_identity_unique'",
            [$database]
        )->getRowArray();

        if ((int) ($hasOldIndex['total'] ?? 0) === 0) {
            if (! $db->fieldExists('no_bpkb_key', 'bpkb')) {
                $db->query(
                    "ALTER TABLE `bpkb`
                    ADD COLUMN `no_bpkb_key` VARCHAR(50)
                        GENERATED ALWAYS AS (
                            CASE
                                WHEN `no_bpkb` IS NULL OR TRIM(`no_bpkb`) IN ('', '-', '0') THEN ''
                                ELSE UPPER(TRIM(`no_bpkb`))
                            END
                        ) STORED"
                );
            }

            if (! $db->fieldExists('no_rangka_key', 'bpkb')) {
                $db->query(
                    "ALTER TABLE `bpkb`
                    ADD COLUMN `no_rangka_key` VARCHAR(50)
                        GENERATED ALWAYS AS (
                            CASE
                                WHEN `no_rangka` IS NULL OR TRIM(`no_rangka`) IN ('', '-', '0') THEN ''
                                ELSE UPPER(TRIM(`no_rangka`))
                            END
                        ) STORED"
                );
            }

            if (! $db->fieldExists('no_mesin_key', 'bpkb')) {
                $db->query(
                    "ALTER TABLE `bpkb`
                    ADD COLUMN `no_mesin_key` VARCHAR(50)
                        GENERATED ALWAYS AS (
                            CASE
                                WHEN `no_mesin` IS NULL OR TRIM(`no_mesin`) IN ('', '-', '0') THEN ''
                                ELSE UPPER(TRIM(`no_mesin`))
                            END
                        ) STORED"
                );
            }

            $db->query(
                "CREATE UNIQUE INDEX `bpkb_identity_unique`
                ON `bpkb` (`plate_number_key`, `no_bpkb_key`, `no_rangka_key`, `no_mesin_key`)"
            );
        }
    }
}
