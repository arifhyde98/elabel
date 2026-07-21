<?php

namespace App\Controllers;

use App\Models\BpkbModel;
use App\Models\SertifikatModel;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('admin'));
        }

        $documentType = (string) ($this->request->getGet('type') ?: 'bpkb');
        $query = trim((string) $this->request->getGet('q'));
        $bpkbResults = [];
        $sertifikatResults = [];

        if ($query !== '' && $documentType === 'bpkb') {
            $bpkbResults = (new BpkbModel())
                ->select('bpkb.*, boxes.box_code, boxes.location')
                ->join('boxes', 'boxes.id = bpkb.box_id', 'left')
                ->where('bpkb.status !=', 'Dihapus')
                ->like('bpkb.plate_number', $query)
                ->orderBy('bpkb.year', 'desc')
                ->orderBy('bpkb.plate_number', 'asc')
                ->findAll(20);
        }

        if ($query !== '' && $documentType === 'sertifikat') {
            $sertifikatResults = (new SertifikatModel())
                ->select('sertifikat_tanah.*, sertifikat_boxes.box_code')
                ->join('sertifikat_boxes', 'sertifikat_boxes.id = sertifikat_tanah.box_id', 'left')
                ->like('sertifikat_tanah.status_penggunaan', $query)
                ->orderBy('sertifikat_tanah.id', 'desc')
                ->findAll(20);
        }

        $db = db_connect();
        $bpkbCount = (new BpkbModel())
            ->where('status !=', 'Dihapus')
            ->countAllResults();
        $sertifikatCount = (new SertifikatModel())->countAllResults();
        $suratPenyerahanCount = $db->tableExists('surat_penyerahan')
            ? $db->table('surat_penyerahan')->countAllResults()
            : 0;

        return view('landing', [
            'documentType' => $documentType,
            'query' => $query,
            'bpkbResults' => $bpkbResults,
            'sertifikatResults' => $sertifikatResults,
            'bpkbCount' => $bpkbCount,
            'sertifikatCount' => $sertifikatCount,
            'suratPenyerahanCount' => $suratPenyerahanCount,
        ]);
    }
}
