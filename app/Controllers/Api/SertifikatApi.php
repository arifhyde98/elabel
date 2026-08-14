<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class SertifikatApi extends ResourceController
{
    protected $format = 'json';
    private const API_KEY = 'SIPAT-ELABEL-SECURE-KEY-2026';

    public function getByNibar($nibar = null)
    {
        // 1. Verify API Key
        $headerKey = $this->request->getHeaderLine('X-API-KEY');
        if ($headerKey !== self::API_KEY) {
            return $this->failUnauthorized('Invalid API Key');
        }

        if (!$nibar) {
            return $this->failValidationError('NIBAR is required');
        }

        $db = \Config\Database::connect();
        
        $builder = $db->table('sertifikat_tanah s');
        $builder->select('s.no_sertipikat, s.nibar, s.spesifikasi, s.nama_pemilik, s.pdf_path, b.box_code, b.lokasi as box_lokasi');
        $builder->join('sertifikat_boxes b', 'b.id = s.box_id', 'left');
        $builder->where('s.nibar', $nibar);
        $result = $builder->get()->getRowArray();

        if (!$result) {
            return $this->respond([
                'status' => 404,
                'message' => 'Sertifikat belum diarsipkan di eLabel',
                'data' => null
            ], 404);
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'no_sertipikat' => $result['no_sertipikat'],
                'nibar'         => $result['nibar'],
                'nama_pemilik'  => $result['nama_pemilik'],
                'pdf_url'       => $result['pdf_path'] ? base_url($result['pdf_path']) : null,
                'is_archived'   => !empty($result['box_code']),
                'box_code'      => $result['box_code'] ?? '-',
                'box_lokasi'    => $result['box_lokasi'] ?? '-'
            ]
        ]);
    }
}
