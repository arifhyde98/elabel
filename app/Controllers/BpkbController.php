<?php

namespace App\Controllers;

use App\Models\BpkbModel;

class BpkbController extends BaseController
{
    private BpkbModel $bpkb;

    public function __construct()
    {
        $this->bpkb = new BpkbModel();
    }

    public function index(): string
    {
        $items = $this->bpkb
            ->select('bpkb.*, boxes.box_code')
            ->join('boxes', 'boxes.id = bpkb.box_id')
            ->where('bpkb.status !=', 'Dihapus')
            ->orderBy('bpkb.year', 'desc')
            ->orderBy('bpkb.plate_number', 'asc')
            ->findAll();

        return view('user/bpkb/index', [
            'items'      => $items,
            'activeMenu' => 'bpkb',
        ]);
    }

    public function download(int $id)
    {
        $item = $this->bpkb->find($id);
        if (! $item || $item['status'] === 'Dihapus' || ! $item['pdf_path']) {
            return redirect()->back()->with('error', 'File BPKB tidak ditemukan.');
        }

        $path = WRITEPATH . $item['pdf_path'];
        if (! is_file($path)) {
            return redirect()->back()->with('error', 'File BPKB tidak ditemukan.');
        }

        return $this->response->download($path, null);
    }
}
