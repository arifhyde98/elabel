<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeBpkbIdentityFields extends Migration
{
    public function up()
    {
        $this->db->query("UPDATE bpkb SET no_bpkb = NULL WHERE no_bpkb IS NOT NULL AND TRIM(no_bpkb) IN ('', '-', '0')");
        $this->db->query("UPDATE bpkb SET no_rangka = NULL WHERE no_rangka IS NOT NULL AND TRIM(no_rangka) IN ('', '-', '0')");
        $this->db->query("UPDATE bpkb SET no_mesin = NULL WHERE no_mesin IS NOT NULL AND TRIM(no_mesin) IN ('', '-', '0')");
    }

    public function down()
    {
        // Placeholder values intentionally not restored.
    }
}
