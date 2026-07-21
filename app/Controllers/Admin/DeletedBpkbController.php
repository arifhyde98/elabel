<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BpkbDeleteModel;
use App\Models\BpkbModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DeletedBpkbController extends BaseController
{
    private BpkbDeleteModel $deletes;
    private BpkbModel $bpkb;
    private UserModel $users;

    public function __construct()
    {
        $this->deletes = new BpkbDeleteModel();
        $this->bpkb    = new BpkbModel();
        $this->users   = new UserModel();
    }

    public function index(): string
    {
        $items = $this->deletes
            ->select('bpkb_deletes.*, users.name as deleted_name')
            ->join('users', 'users.id = bpkb_deletes.deleted_by')
            ->orderBy('bpkb_deletes.deleted_at', 'desc')
            ->findAll();

        return view('admin/deleted_bpkb/index', [
            'items'      => $items,
            'activeMenu' => 'deleted',
        ]);
    }

    public function create(): string
    {
        $items = $this->bpkb
            ->select('bpkb.*, boxes.box_code, users.name as input_name')
            ->join('boxes', 'boxes.id = bpkb.box_id')
            ->join('users', 'users.id = bpkb.input_by')
            ->where('bpkb.status !=', 'Dihapus')
            ->orderBy('bpkb.year', 'desc')
            ->orderBy('bpkb.plate_number', 'asc')
            ->findAll();

        return view('admin/deleted_bpkb/create', [
            'items'      => $items,
            'activeMenu' => 'deleted',
        ]);
    }

    public function show(int $id)
    {
        $item = $this->deletes
            ->select('bpkb_deletes.*, users.name as deleted_name')
            ->join('users', 'users.id = bpkb_deletes.deleted_by', 'left')
            ->where('bpkb_deletes.id', $id)
            ->first();

        if (! $item) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'Data BPKB keluar tidak ditemukan.');
        }

        return view('admin/deleted_bpkb/show', [
            'item'      => $item,
            'activeMenu' => 'deleted',
        ]);
    }

    public function restore(int $id)
    {
        $record = $this->deletes->find($id);
        if (! $record) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'Data tidak ditemukan.');
        }

        $restorePassword = (string) $this->request->getPost('restore_password');
        if ($restorePassword === '' || ! $this->isCurrentUserPasswordValid($restorePassword)) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'Password login tidak valid. Data BPKB tidak direstore.');
        }

        $newId = $this->bpkb->insert([
            'box_id'       => (int) ($record['box_id'] ?? 0),
            'year'         => (int) ($record['year'] ?? 0),
            'vehicle_type' => $record['vehicle_type'] ?? null,
            'plate_number' => $record['plate_number'] ?? null,
            'no_bpkb'      => $record['no_bpkb'] ?? null,
            'no_rangka'    => $record['no_rangka'] ?? null,
            'no_mesin'     => $record['no_mesin'] ?? null,
            'merek'        => $record['merek'] ?? null,
            'tipe'         => $record['tipe'] ?? null,
            'isi_silinder' => $record['isi_silinder'] ?? null,
            'warna'        => $record['warna'] ?? null,
            'pengguna'     => $record['pengguna'] ?? null,
            'status'       => 'Tersedia',
            'pdf_path'     => $record['pdf_path'] ?? null,
            'input_by'     => (int) ($record['input_by'] ?? session()->get('user_id')),
        ]);

        $this->deletes->delete($id);
        $this->logActivity('restore', 'BPKB Keluar', 'Merestore BPKB ' . ($record['plate_number'] ?? '-') . '.', 'bpkb', (int) $newId);

        return redirect()->to(site_url('admin/bpkb-deleted'))->with('success', 'Data BPKB berhasil direstore.');
    }

    public function exportExcel()
    {
        $items = $this->deletes
            ->select('bpkb_deletes.*, users.name as deleted_name')
            ->join('users', 'users.id = bpkb_deletes.deleted_by')
            ->orderBy('bpkb_deletes.deleted_at', 'desc')
            ->findAll();

        $filename = 'bpkb-keluar-' . date('Ymd-His') . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['No', 'No. Polisi', 'Box', 'Tahun', 'Tanggal Hapus', 'Alasan', 'Keterangan', 'User'],
        ], null, 'A1');

        $rowIndex = 2;
        $i = 1;
        foreach ($items as $item) {
            $sheet->fromArray([[
                $i++,
                $item['plate_number'] ?? '',
                $item['box_code'] ?? '',
                $item['year'] ?? '',
                isset($item['deleted_at']) ? date('Y-m-d', strtotime((string) $item['deleted_at'])) : '',
                $item['reason'] ?? '',
                $item['reason_detail'] ?? '',
                $item['deleted_name'] ?? '',
            ]], null, 'A' . $rowIndex);
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'bpkb-del-');
        if ($tempFile === false) {
            return $this->response;
        }
        $writer->save($tempFile);
        $contents = file_get_contents($tempFile);
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($contents ?: '');
    }

    public function destroy(int $id)
    {
        $record = $this->deletes->find($id);
        if (! $record) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'Data tidak ditemukan.');
        }

        $deletePassword = (string) $this->request->getPost('delete_password');
        if ($deletePassword === '' || ! $this->isCurrentUserPasswordValid($deletePassword)) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'Password login tidak valid. Data BPKB keluar tidak dihapus.');
        }

        // Pastikan tidak ada relasi aktif (mis. loan) sebelum hapus permanen.
        $loanModel = new \App\Models\LoanModel();
        $loanCount = $loanModel->where('bpkb_id', $record['bpkb_id'])->countAllResults();
        if ($loanCount > 0) {
            return redirect()->to(site_url('admin/bpkb-deleted'))
                ->with('error', 'Tidak bisa hapus permanen: masih ada data peminjaman terkait.');
        }
        $this->deletes->delete($id);
        $this->logActivity('permanent_delete', 'BPKB Keluar', 'Menghapus permanen BPKB keluar ' . ($record['plate_number'] ?? '-') . '.', 'bpkb_deletes', $id);

        return redirect()->to(site_url('admin/bpkb-deleted'))->with('success', 'Data BPKB keluar terhapus permanen.');
    }

    private function isCurrentUserPasswordValid(string $password): bool
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0 || $password === '') {
            return false;
        }

        $user = $this->users->find($userId);
        if (! $user || empty($user['password'])) {
            return false;
        }

        return password_verify($password, (string) $user['password']);
    }
}
