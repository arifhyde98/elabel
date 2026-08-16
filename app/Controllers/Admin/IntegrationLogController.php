<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\SyncService;

class IntegrationLogController extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();

        $logs = [];
        try {
            $logs = $db->table('integration_audit_logs')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching integration_audit_logs: ' . $e->getMessage());
        }

        $queues = [];
        try {
            $queues = $db->table('sync_queue')
                ->whereIn('status', ['PENDING', 'FAILED'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching sync_queue: ' . $e->getMessage());
        }

        return view('admin/integration_logs/index', [
            'activeMenu' => 'integration_logs',
            'logs'       => $logs,
            'queues'     => $queues,
        ]);
    }

    public function retry(int $queueId)
    {
        $db = \Config\Database::connect();
        $item = $db->table('sync_queue')->where('id', $queueId)->get()->getRowArray();

        if (!$item) {
            return redirect()->back()->with('error', 'Antrean sinkronisasi tidak ditemukan.');
        }

        $payload = json_decode($item['payload'], true) ?? [];
        $res = SyncService::dispatch($item['target_url'], $payload);

        if ($res['success']) {
            $db->table('sync_queue')->where('id', $queueId)->update([
                'status'     => 'DONE',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $db->table('integration_audit_logs')->where('event_id', $item['event_id'])->update([
                'sync_status'   => 'SUCCESS',
                'error_message' => null
            ]);

            if (!empty($payload['nibar'])) {
                $cleanNibar = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($payload['nibar']));
                $db->table('sertifikat_tanah')->where('nibar_key', $cleanNibar)->update(['sync_status' => 'synced']);
            }

            return redirect()->back()->with('success', 'Sinkronisasi ulang berhasil dikirim ke SIPAT!');
        } else {
            $retries = (int)$item['retry_count'] + 1;
            $db->table('sync_queue')->where('id', $queueId)->update([
                'retry_count'   => $retries,
                'status'        => ($retries >= (int)$item['max_retries']) ? 'FAILED' : 'PENDING',
                'last_error'    => $res['error'],
                'next_retry_at' => date('Y-m-d H:i:s', time() + 300),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);

            return redirect()->back()->with('error', 'Gagal mengirim sinkronisasi: ' . ($res['error'] ?? 'Server target tidak merespons'));
        }
    }
}
