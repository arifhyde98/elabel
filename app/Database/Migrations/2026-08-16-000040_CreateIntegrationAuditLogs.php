<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIntegrationAuditLogs extends Migration
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
            'correlation_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'nibar' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'event_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
            ],
            'source_system' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'direction' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'changes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'sync_status' => [
                'type'       => 'ENUM',
                'constraint' => ['SUCCESS', 'FAILED', 'PENDING'],
                'default'    => 'PENDING',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_version' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('event_id');
        $this->forge->addKey('nibar');
        $this->forge->addKey('sync_status');
        $this->forge->addKey('created_at');
        $this->forge->createTable('integration_audit_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('integration_audit_logs', true);
    }
}
