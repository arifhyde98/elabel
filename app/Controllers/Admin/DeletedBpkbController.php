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

    public function edit(int $id)
    {
        $item = $this->deletes->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'Data BPKB keluar tidak ditemukan.');
        }

        return view('admin/deleted_bpkb/edit', [
            'item'      => $item,
            'activeMenu' => 'deleted',
        ]);
    }

    public function viewBpkbPdf(int $id)
    {
        $item = $this->deletes->find($id);
        if (! $item || empty($item['pdf_path'])) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'File PDF BPKB tidak ditemukan.');
        }

        $path = WRITEPATH . $item['pdf_path'];
        if (! is_file($path)) {
            return redirect()->to(site_url('admin/bpkb-deleted/' . $id))->with('error', 'File PDF BPKB tidak ditemukan.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="bpkb-keluar-' . $id . '.pdf"')
            ->setBody(file_get_contents($path) ?: '');
    }

    public function viewSupportDoc(int $id)
    {
        $item = $this->deletes->find($id);
        if (! $item || empty($item['support_doc_path'])) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'Dokumen pendukung tidak ditemukan.');
        }

        $path = WRITEPATH . $item['support_doc_path'];
        if (! is_file($path)) {
            return redirect()->to(site_url('admin/bpkb-deleted/' . $id))->with('error', 'Dokumen pendukung tidak ditemukan.');
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $contentTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];
        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Disposition', 'inline; filename="dokumen-pendukung-bpkb-keluar-' . $id . '.' . $extension . '"')
            ->setBody(file_get_contents($path) ?: '');
    }

    public function update(int $id)
    {
        $item = $this->deletes->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/bpkb-deleted'))->with('error', 'Data BPKB keluar tidak ditemukan.');
        }

        $rules = [
            'year'          => 'required|integer',
            'vehicle_type'  => 'required|in_list[R4,R2]',
            'plate_number'  => 'required|max_length[20]',
            'box_code'      => 'permit_empty|max_length[50]',
            'no_bpkb'       => 'permit_empty|max_length[50]',
            'nibar'         => 'permit_empty|max_length[100]',
            'no_rangka'     => 'permit_empty|max_length[50]',
            'no_mesin'      => 'permit_empty|max_length[50]',
            'merek'         => 'permit_empty|max_length[100]',
            'tipe'          => 'permit_empty|max_length[100]',
            'isi_silinder'  => 'permit_empty|max_length[50]',
            'warna'         => 'permit_empty|max_length[100]',
            'pengguna'      => 'permit_empty|max_length[100]',
            'reason'        => 'required|max_length[50]',
            'reason_detail' => 'permit_empty',
            'support_doc'   => 'if_exist|max_size[support_doc,5120]|ext_in[support_doc,pdf,jpg,jpeg,png]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supportDocPath = $item['support_doc_path'] ?? null;
        $file = $this->request->getFile('support_doc');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $oldSupportDocPath = $supportDocPath;
            $supportDocPath = $this->storeSupportDoc($file, (string) $this->request->getPost('plate_number'));

            if ($oldSupportDocPath && $oldSupportDocPath !== $supportDocPath) {
                $oldAbsolutePath = WRITEPATH . $oldSupportDocPath;
                if (is_file($oldAbsolutePath)) {
                    @unlink($oldAbsolutePath);
                }
            }
        }

        $plateNumber = $this->normalizeUpperText((string) $this->request->getPost('plate_number'));
        try {
            $this->deletes->skipValidation(true)->update($id, [
                'year'             => (int) $this->request->getPost('year'),
                'vehicle_type'     => $this->normalizeVehicleType((string) $this->request->getPost('vehicle_type')) ?? 'R4',
                'box_code'         => $this->normalizeUpperText((string) $this->request->getPost('box_code')),
                'plate_number'     => $plateNumber,
                'no_bpkb'          => $this->normalizeNullableUpperText((string) $this->request->getPost('no_bpkb')),
                'nibar'            => $this->normalizeNullableUpperText((string) $this->request->getPost('nibar')),
                'no_rangka'        => $this->normalizeNullableUpperText((string) $this->request->getPost('no_rangka')),
                'no_mesin'         => $this->normalizeNullableUpperText((string) $this->request->getPost('no_mesin')),
                'merek'            => $this->normalizeNullableText((string) $this->request->getPost('merek')),
                'tipe'             => $this->normalizeNullableText((string) $this->request->getPost('tipe')),
                'isi_silinder'     => $this->normalizeNullableText((string) $this->request->getPost('isi_silinder')),
                'warna'            => $this->normalizeNullableText((string) $this->request->getPost('warna')),
                'pengguna'         => $this->normalizeNullableText((string) $this->request->getPost('pengguna')),
                'reason'           => (string) $this->request->getPost('reason'),
                'reason_detail'    => $this->normalizeNullableText((string) $this->request->getPost('reason_detail')),
                'support_doc_path' => $supportDocPath,
            ]);
        } finally {
            $this->deletes->skipValidation(false);
        }
        $this->logActivity('update', 'BPKB Keluar', 'Mengubah BPKB keluar ' . $plateNumber . '.', 'bpkb_deletes', $id);

        return redirect()->to(site_url('admin/bpkb-deleted'))->with('success', 'Data BPKB keluar berhasil diperbarui.');
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
            'nibar'        => $record['nibar'] ?? null,
            'no_rangka'    => $record['no_rangka'] ?? null,
            'no_mesin'     => $record['no_mesin'] ?? null,
            'merek'        => $record['merek'] ?? null,
            'tipe'         => $record['tipe'] ?? null,
            'isi_silinder' => $record['isi_silinder'] ?? null,
            'warna'        => $record['warna'] ?? null,
            'pengguna'     => $record['pengguna'] ?? null,
            'status'       => 'Tersedia',
            'pdf_path'     => $record['pdf_path'] ?? null,
            'input_by'     => $this->resolveRestoreInputBy($record),
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
                ->with('error', 'Tidak bisa hapus permanen: masih ada data permintaan scan terkait.');
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

    private function resolveRestoreInputBy(array $record): int
    {
        $recordInputBy = (int) ($record['input_by'] ?? 0);
        if ($recordInputBy > 0 && $this->users->find($recordInputBy)) {
            return $recordInputBy;
        }

        return (int) session()->get('user_id');
    }

    private function normalizeVehicleType(string $value): ?string
    {
        $value = strtoupper(trim($value));
        return in_array($value, ['R4', 'R2'], true) ? $value : null;
    }

    private function normalizeUpperText(string $value): string
    {
        return strtoupper(trim($value));
    }

    private function normalizeNullableUpperText(string $value): ?string
    {
        $value = $this->normalizeUpperText($value);
        return $value === '' ? null : $value;
    }

    private function normalizeNullableText(string $value): ?string
    {
        $value = trim($value);
        return $value === '' ? null : $value;
    }

    private function storeSupportDoc($file, string $plateNumber): string
    {
        $dir = WRITEPATH . 'uploads/bpkb-deletes';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $extension = strtolower((string) $file->getClientExtension());
        if ($extension === '') {
            $extension = strtolower((string) $file->getExtension());
        }
        if ($extension === '') {
            $extension = 'dat';
        }

        $safePlate = preg_replace('/[^A-Za-z0-9_-]+/', '-', strtoupper(trim($plateNumber))) ?: 'BPKB';
        $filename = $safePlate . '-' . uniqid('', true) . '.' . $extension;
        $file->move($dir, $filename);

        return 'uploads/bpkb-deletes/' . $filename;
    }
}
