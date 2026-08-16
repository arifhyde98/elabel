<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\SyncService;

class SyncRetryCommand extends BaseCommand
{
    protected $group       = 'Integration';
    protected $name        = 'sync:retry';
    protected $description = 'Proses ulang antrean sinkronisasi yang pending/gagal.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $pending = $db->table('sync_queue')
            ->where('status', 'PENDING')
            ->where('retry_count <', 10)
            ->get()
            ->getResultArray();

        if (empty($pending)) {
            CLI::write('Tidak ada antrean sinkronisasi yang pending.', 'green');
            return;
        }

        CLI::write('Memproses ' . count($pending) . ' antrean sinkronisasi...', 'yellow');

        foreach ($pending as $item) {
            $payload = json_decode($item['payload'], true) ?? [];
            $res = SyncService::dispatch($item['target_url'], $payload);

            if ($res['success']) {
                $db->table('sync_queue')->where('id', $item['id'])->update([
                    'status'     => 'DONE',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $db->table('integration_audit_logs')->where('event_id', $item['event_id'])->update([
                    'sync_status'   => 'SUCCESS',
                    'error_message' => null
                ]);
                // If it's a sertifikat_tanah update, update sync_status = 'synced'
                if (!empty($payload['nibar'])) {
                    $cleanNibar = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($payload['nibar']));
                    $db->table('sertifikat_tanah')->where('nibar_key', $cleanNibar)->update(['sync_status' => 'synced']);
                }
                CLI::write("Success Event: {$item['event_id']}", 'green');
            } else {
                $retries = (int)$item['retry_count'] + 1;
                $status = ($retries >= (int)$item['max_retries']) ? 'FAILED' : 'PENDING';
                $db->table('sync_queue')->where('id', $item['id'])->update([
                    'retry_count'   => $retries,
                    'status'        => $status,
                    'last_error'    => $res['error'],
                    'next_retry_at' => date('Y-m-d H:i:s', time() + 300),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
                CLI::write("Failed Event {$item['event_id']} (Attempt {$retries}): " . $res['error'], 'red');
            }
        }
    }
}
