<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SertifikatModel;

class EbmdTanahController extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        
        // 1. Fetch real-time land assets from SIPAT API
        $client = \Config\Services::curlrequest();
        $sipatApiUrl = env('SIPAT_API_URL', 'https://sipat-donggala.my.id/api/v1/aset-tanah');
        $sipatApiKey = env('SIPAT_API_KEY', 'SIPAT-ELABEL-SECURE-KEY-2026');

        $sipatAssets = cache('sipat_assets_cache');
        $apiConnected = !empty($sipatAssets);
        $apiError = '';

        if (!$apiConnected) {
            try {
                $response = $client->request('GET', $sipatApiUrl, [
                    'headers' => [
                        'X-API-KEY' => $sipatApiKey,
                        'Accept'    => 'application/json'
                    ],
                    'http_errors'     => false,
                    'connect_timeout' => 10,
                    'timeout'         => 30
                ]);

                if ($response->getStatusCode() === 200) {
                    $json = json_decode($response->getBody(), true);
                    if (isset($json['data']) && is_array($json['data'])) {
                        $sipatAssets = $json['data'];
                        $apiConnected = true;
                        // Cache data selama 5 menit (300 detik) untuk kecepatan akses & menghindari Cloudflare timeout
                        cache()->save('sipat_assets_cache', $sipatAssets, 300);
                    }
                } else {
                    $apiError = 'Gagal terhubung ke API SIPAT. Code: ' . $response->getStatusCode();
                }
            } catch (\Throwable $e) {
                $apiError = 'Koneksi API SIPAT terputus: ' . $e->getMessage();
            }
        }

        // 2. Fetch local eLabel certificates & box info for cross-referencing NIBAR
        $localCerts = [];
        try {
            $localCerts = $db->table('sertifikat_tanah st')
                ->select('st.*, sb.box_code, sb.lokasi as lokasi_box')
                ->join('sertifikat_boxes sb', 'sb.id = st.box_id', 'left')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching localCerts: ' . $e->getMessage());
        }

        $archiveMap = [];
        foreach ($localCerts as $cert) {
            $nibarRaw = trim((string) ($cert['nibar'] ?? ''));
            if ($nibarRaw !== '') {
                $cleanKey = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($nibarRaw));
                if ($cleanKey !== '') {
                    $archiveMap[$cleanKey] = $cert;
                }
            }
        }

        return view('admin/ebmd_tanah/index', [
            'activeMenu'   => 'ebmd_tanah',
            'title'        => 'Tanah E-BMD',
            'sipatAssets'  => $sipatAssets,
            'archiveMap'   => $archiveMap,
            'apiConnected' => $apiConnected,
            'apiError'     => $apiError,
        ]);
    }
}
