<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\SyncService;

class IntegrationApi extends BaseController
{
    private function validateApiKey(): bool
    {
        $expectedKey = env('SIPAT_API_KEY', 'SIPAT-ELABEL-SECURE-KEY-2026');
        $headerKey   = $this->request->getHeaderLine('X-API-KEY');
        $queryKey    = $this->request->getGet('api_key');
        $providedKey = !empty($headerKey) ? $headerKey : $queryKey;

        return !empty($providedKey) && hash_equals($expectedKey, $providedKey);
    }

    public function assetUpdated()
    {
        if (!$this->validateApiKey()) {
            return $this->response->setJSON([
                'status'  => 401,
                'message' => 'Unauthorized: Invalid or missing API Key'
            ])->setStatusCode(401);
        }

        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $eventId  = $json['event_id'] ?? null;
        $nibar    = trim((string) ($json['nibar'] ?? ''));
        $source   = $json['source'] ?? 'sipat';
        $changes  = $json['changes'] ?? [];
        $reason   = $json['reason'] ?? 'Pembaruan data dari SIPAT';
        $operator = $json['operator'] ?? 'SIPAT System';

        // Loop prevention check
        if ($source === 'elabel') {
            return $this->response->setJSON([
                'status'  => 200,
                'message' => 'Loop prevention: Source is elabel. Ignored.'
            ]);
        }

        if (empty($nibar)) {
            return $this->response->setJSON([
                'status'  => 400,
                'message' => 'Bad Request: Field NIBAR wajib diisi'
            ])->setStatusCode(400);
        }

        // Idempotency check
        if ($eventId && SyncService::isEventProcessed($eventId)) {
            return $this->response->setJSON([
                'status'  => 200,
                'message' => 'Event already processed'
            ]);
        }

        $db = \Config\Database::connect();
        $cleanNibar = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($nibar));
        $cert = $db->table('sertifikat_tanah')->where('nibar_key', $cleanNibar)->get()->getRowArray();

        if (!$cert) {
            // Also try raw nibar search
            $cert = $db->table('sertifikat_tanah')->where('nibar', $nibar)->get()->getRowArray();
        }

        if (!$cert) {
            return $this->response->setJSON([
                'status'  => 404,
                'message' => "Sertifikat dengan NIBAR {$nibar} tidak ditemukan di eLabel"
            ])->setStatusCode(404);
        }

        // Whitelist allowed fields for eLabel
        $allowedMap = [
            'luas'              => 'luas',
            'alamat'            => 'alamat',
            'harga_perolehan'   => 'nilai_perolehan',
            'nilai_perolehan'   => 'nilai_perolehan',
            'tanggal_perolehan' => 'tanggal_perolehan',
            'dasar_perolehan'   => 'cara_perolehan',
            'cara_perolehan'    => 'cara_perolehan',
            'opd'               => 'dinas',
            'dinas'             => 'dinas',
            'peruntukan'        => 'status_penggunaan',
            'status_penggunaan' => 'status_penggunaan',
            'nama_aset'         => 'spesifikasi',
            'spesifikasi'       => 'spesifikasi'
        ];

        $updateData = [];
        foreach ($changes as $field => $val) {
            if (isset($allowedMap[$field])) {
                $dbField = $allowedMap[$field];
                $updateData[$dbField] = is_array($val) ? ($val['new'] ?? null) : $val;
            }
        }

        if (!empty($updateData)) {
            $updateData['sync_status'] = 'synced';
            $updateData['updated_at'] = date('Y-m-d H:i:s');
            $db->table('sertifikat_tanah')->where('id', $cert['id'])->update($updateData);
        }

        // Audit Log
        SyncService::logAudit([
            'event_id'       => $eventId ?: bin2hex(random_bytes(16)),
            'correlation_id' => $json['correlation_id'] ?? null,
            'nibar'          => $nibar,
            'event_name'     => 'ASSET_DATA_CHANGED',
            'source_system'  => $source,
            'direction'      => 'inbound',
            'changes'        => $changes,
            'reason'         => $reason,
            'sync_status'    => 'SUCCESS',
            'created_by'     => $operator,
        ]);

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Sinkronisasi data sertifikat berhasil diperbarui di eLabel'
        ]);
    }
}
