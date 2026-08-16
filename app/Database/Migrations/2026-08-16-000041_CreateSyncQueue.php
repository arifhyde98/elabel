<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSyncQueue extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'target_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'payload' => [
                'type' => 'TEXT',
            ],
            'retry_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'max_retries' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 10,
            ],
            'next_retry_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['PENDING', 'PROCESSING', 'DONE', 'FAILED'],
                'default'    => 'PENDING',
            ],
            'last_error' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('event_id');
        $this->forge->addKey('status');
        $this->forge->createTable('sync_queue', true);
    }

    public function down()
    {
        $this->forge->dropTable('sync_queue', true);
    }
}
