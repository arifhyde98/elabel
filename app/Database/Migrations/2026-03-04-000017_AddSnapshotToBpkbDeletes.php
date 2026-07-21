<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSnapshotToBpkbDeletes extends Migration
{
    public function up()
    {
        $db = $this->db;

        $fkExists = $db->query(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'bpkb_deletes'
             AND CONSTRAINT_NAME = 'bpkb_deletes_bpkb_id_foreign'"
        )->getNumRows() > 0;

        if ($fkExists) {
            $db->query("ALTER TABLE `bpkb_deletes` DROP FOREIGN KEY `bpkb_deletes_bpkb_id_foreign`");
        }

        $fields = [
            'box_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'bpkb_id',
            ],
            'box_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'box_id',
            ],
            'year' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'box_code',
            ],
            'vehicle_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'year',
            ],
            'plate_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'vehicle_type',
            ],
            'no_bpkb' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'plate_number',
            ],
            'no_rangka' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'no_bpkb',
            ],
            'no_mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'no_rangka',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'no_mesin',
            ],
            'pdf_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status',
            ],
            'input_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'pdf_path',
            ],
            'support_doc_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'reason_detail',
            ],
        ];

        foreach ($fields as $name => $def) {
            if (! $db->fieldExists($name, 'bpkb_deletes')) {
                $this->forge->addColumn('bpkb_deletes', [$name => $def]);
            }
        }
    }

    public function down()
    {
        // No down migration (data snapshot columns are intentionally retained).
    }
}
