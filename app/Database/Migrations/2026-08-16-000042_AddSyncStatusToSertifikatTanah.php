<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSyncStatusToSertifikatTanah extends Migration
{
    public function up()
    {
        $fields = [
            'sync_status' => [
                'type'       => 'ENUM',
                'constraint' => ['synced', 'pending', 'failed'],
                'default'    => 'synced',
                'after'      => 'dinas',
            ],
            'data_version' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
                'after'      => 'sync_status',
            ],
        ];

        $this->forge->addColumn('sertifikat_tanah', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sertifikat_tanah', ['sync_status', 'data_version']);
    }
}
