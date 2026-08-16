<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class SertifikatApi extends ResourceController
{
    protected $format = 'json';
    public function getByNibar($nibar = null)
    {
        // 1. Verify API Key
        $apiKey = env('ELABEL_API_KEY', 'SIPAT-ELABEL-SECURE-KEY-2026');
        $headerKey = $this->request->getHeaderLine('X-API-KEY');
        if ($headerKey !== $apiKey) {
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

        // Generate Signed URL for PDF access
        $pdfUrl = null;
        if ($result['pdf_path']) {
            $expires = time() + (60 * 30); // 30 minutes
            $secret = env('ELABEL_URL_SIGNATURE_KEY', 'SECRET-URL-KEY-4d24d5364459d834d846353dceae3beb');
            $signature = hash_hmac('sha256', $result['nibar'] . '|' . $expires, $secret);
            $pdfUrl = base_url('api/v1/sertifikat-pdf/' . urlencode($result['nibar']) . '?expires=' . $expires . '&signature=' . $signature);
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'no_sertipikat' => $result['no_sertipikat'],
                'nibar'         => $result['nibar'],
                'nama_pemilik'  => $result['nama_pemilik'],
                'pdf_url'       => $pdfUrl,
                'is_archived'   => !empty($result['box_code']),
                'box_code'      => $result['box_code'] ?? '-',
                'box_lokasi'    => $result['box_lokasi'] ?? '-'
            ]
        ]);
    }
    public function getAllNibar()
    {
        // 1. Verify API Key
        $apiKey = env('ELABEL_API_KEY', 'SIPAT-ELABEL-SECURE-KEY-2026');
        $headerKey = $this->request->getHeaderLine('X-API-KEY');
        if ($headerKey !== $apiKey) {
            return $this->failUnauthorized('Invalid API Key');
        }

        $db = \Config\Database::connect();
        
        // Fetch only nibar where box_id is not null (meaning it's archived physically)
        // Or should we just fetch all? The requirement is to check if it's in eLabel.
        // If it's in eLabel, it's considered physically archived. 
        // Let's fetch all nibar from sertifikat_tanah.
        $builder = $db->table('sertifikat_tanah');
        $builder->select('nibar');
        // Ensure NIBAR is not empty
        $builder->where('nibar !=', '');
        $builder->where('nibar IS NOT NULL');
        
        $results = $builder->get()->getResultArray();
        
        // Extract just the nibar values into a flat array
        $nibarList = array_column($results, 'nibar');

        return $this->respond([
            'status' => 200,
            'message' => 'Success',
            'data' => $nibarList
        ]);
    }

    public function viewPdf($nibar = null)
    {
        $expires = $this->request->getGet('expires');
        $signature = $this->request->getGet('signature');
        
        if (!$expires || !$signature) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak: Signature atau Expires tidak ada.');
        }

        if (time() > (int)$expires) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak: Link sudah kedaluwarsa.');
        }

        $secret = env('ELABEL_URL_SIGNATURE_KEY', 'SECRET-URL-KEY-4d24d5364459d834d846353dceae3beb');
        $expectedSignature = hash_hmac('sha256', $nibar . '|' . $expires, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak: Signature tidak valid.');
        }

        if (!$nibar) {
            return $this->failValidationError('NIBAR is required');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sertifikat_tanah');
        $builder->select('pdf_path');
        $builder->where('nibar', $nibar);
        $result = $builder->get()->getRowArray();

        if (!$result || empty($result['pdf_path'])) {
            return $this->response->setStatusCode(404)->setBody('File PDF sertifikat tidak ditemukan.');
        }

        $path = WRITEPATH . $result['pdf_path'];
        if (!is_file($path)) {
            return $this->response->setStatusCode(404)->setBody('File fisik PDF tidak ditemukan di server eLabel.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="sertifikat-' . urlencode($nibar) . '.pdf"')
            ->setBody(file_get_contents($path));
    }
}
