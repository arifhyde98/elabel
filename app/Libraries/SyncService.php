<?php

namespace App\Libraries;

use Config\Database;

class SyncService
{
    public static function logAudit(array $data): bool
    {
        try {
            $db = Database::connect();
            return $db->table('integration_audit_logs')->insert([
                'event_id'       => $data['event_id'] ?? bin2hex(random_bytes(16)),
                'correlation_id' => $data['correlation_id'] ?? null,
                'nibar'          => $data['nibar'] ?? '',
                'event_name'     => $data['event_name'] ?? 'ASSET_DATA_CHANGED',
                'source_system'  => $data['source_system'] ?? 'elabel',
                'direction'      => $data['direction'] ?? 'outbound',
                'changes'        => isset($data['changes']) ? (is_string($data['changes']) ? $data['changes'] : json_encode($data['changes'])) : null,
                'reason'         => $data['reason'] ?? null,
                'sync_status'    => $data['sync_status'] ?? 'PENDING',
                'error_message'  => $data['error_message'] ?? null,
                'data_version'   => $data['data_version'] ?? 1,
                'created_by'     => $data['created_by'] ?? (session()->get('user_id') ? 'User #' . session()->get('user_id') : 'SYSTEM'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SyncService::logAudit error: ' . $e->getMessage());
            return false;
        }
    }

    public static function isEventProcessed(string $eventId): bool
    {
        try {
            $db = Database::connect();
            $row = $db->table('integration_audit_logs')
                ->where('event_id', $eventId)
                ->where('sync_status', 'SUCCESS')
                ->get()
                ->getRowArray();
            return !empty($row);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function enqueue(string $eventId, string $targetUrl, array $payload): bool
    {
        try {
            $db = Database::connect();
            return $db->table('sync_queue')->insert([
                'event_id'      => $eventId,
                'target_url'    => $targetUrl,
                'payload'       => json_encode($payload),
                'retry_count'   => 0,
                'max_retries'   => 10,
                'next_retry_at' => date('Y-m-d H:i:s'),
                'status'        => 'PENDING',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SyncService::enqueue error: ' . $e->getMessage());
            return false;
        }
    }

    public static function dispatch(string $targetUrl, array $payload, ?string $apiKey = null): array
    {
        $apiKey = $apiKey ?? (env('SIPAT_API_KEY') ?: 'SIPAT-ELABEL-SECURE-KEY-2026');
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->request('POST', $targetUrl, [
                'headers' => [
                    'X-API-KEY'    => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json'
                ],
                'json'            => $payload,
                'http_errors'     => false,
                'connect_timeout' => 5,
                'timeout'         => 10
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 200 || $statusCode === 201) {
                return ['success' => true, 'code' => $statusCode, 'data' => $body];
            } else {
                return ['success' => false, 'code' => $statusCode, 'error' => $body['message'] ?? 'HTTP Error ' . $statusCode];
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'code' => 0, 'error' => $e->getMessage()];
        }
    }
}
