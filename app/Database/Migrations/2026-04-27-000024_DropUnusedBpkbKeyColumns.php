<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropUnusedBpkbKeyColumns extends Migration
{
    public function up()
    {
        $columns = ['no_bpkb_key', 'no_rangka_key', 'no_mesin_key'];

        foreach ($columns as $column) {
            if ($this->db->fieldExists($column, 'bpkb')) {
                $this->db->query("ALTER TABLE `bpkb` DROP COLUMN `{$column}`");
            }
        }
    }

    public function down()
    {
        if (! $this->db->fieldExists('no_bpkb_key', 'bpkb')) {
            $this->db->query(
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

        if (! $this->db->fieldExists('no_rangka_key', 'bpkb')) {
            $this->db->query(
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

        if (! $this->db->fieldExists('no_mesin_key', 'bpkb')) {
            $this->db->query(
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
    }
}
