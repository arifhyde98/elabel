<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BpkbModel;
use App\Models\LoanHistoryModel;
use App\Models\LoanModel;
use App\Models\SertifikatModel;

class LoanController extends BaseController
{
    private LoanModel $loans;
    private LoanHistoryModel $histories;
    private BpkbModel $bpkb;
    private SertifikatModel $sertifikat;

    public function __construct()
    {
        $this->loans     = new LoanModel();
        $this->histories = new LoanHistoryModel();
        $this->bpkb      = new BpkbModel();
        $this->sertifikat = new SertifikatModel();
    }

    public function index(string $documentType = 'bpkb'): string
    {
        $documentType = strtolower($documentType);
        if (! in_array($documentType, ['bpkb', 'sertifikat'], true)) {
            $documentType = 'bpkb';
        }

        $items         = [];
        $availableBpkb = [];

        if ($documentType === 'bpkb') {
            $items = $this->loans
                ->select('loans.*, bpkb.plate_number, bpkb.year as bpkb_year, bpkb.pdf_path as document_pdf_path, boxes.box_code, COALESCE(users.name, loans.requester_name) as requester_name')
                ->join('bpkb', 'bpkb.id = loans.bpkb_id')
                ->join('boxes', 'boxes.id = bpkb.box_id')
                ->join('users', 'users.id = loans.requester_id', 'left')
                ->orderBy('loans.requested_at', 'desc')
                ->findAll();
            $availableBpkb = $this->bpkb
                ->select('bpkb.id, bpkb.plate_number, bpkb.no_bpkb, bpkb.year, bpkb.vehicle_type, boxes.box_code')
                ->join('boxes', 'boxes.id = bpkb.box_id')
                ->where('bpkb.status', 'Tersedia')
                ->orderBy('bpkb.plate_number', 'asc')
                ->findAll();
        } elseif ($this->loanFieldExists('sertifikat_id')) {
            $items = $this->loans
                ->select('loans.*, sertifikat_tanah.no_sertipikat, sertifikat_tanah.lokasi, sertifikat_tanah.pdf_path as document_pdf_path, sertifikat_boxes.box_code, COALESCE(users.name, loans.requester_name) as requester_name')
                ->join('sertifikat_tanah', 'sertifikat_tanah.id = loans.sertifikat_id')
                ->join('sertifikat_boxes', 'sertifikat_boxes.id = sertifikat_tanah.box_id', 'left')
                ->join('users', 'users.id = loans.requester_id', 'left')
                ->orderBy('loans.requested_at', 'desc')
                ->findAll();
        }

        return view('admin/loans/index', [
            'items'          => $items,
            'availableBpkb'  => $availableBpkb,
            'documentType'   => $documentType,
            'documentLabel'  => $documentType === 'sertifikat' ? 'Sertipikat Tanah' : 'BPKB',
            'activeMenu'     => 'loans',
        ]);
    }

    public function storeManual()
    {
        $rules = [
            'bpkb_id'         => 'required|integer',
            'requester_name'  => 'required|min_length[3]|max_length[100]',
            'requester_phone' => 'permit_empty|min_length[8]|max_length[30]',
            'requester_email' => 'permit_empty|valid_email|max_length[150]',
            'requester_org'   => 'permit_empty|max_length[150]',
            'requester_note'  => 'permit_empty|max_length[255]',
            'note'            => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bpkbId = (int) $this->request->getPost('bpkb_id');
        $item   = $this->bpkb->find($bpkbId);

        if (! $item || $item['status'] !== 'Tersedia') {
            return redirect()->back()->withInput()->with('error', 'BPKB tidak tersedia untuk diminta scan.');
        }

        $db = db_connect();
        $db->transStart();

        $loanId = $this->loans->insert([
            'bpkb_id'          => $bpkbId,
            'requester_id'     => null,
            'requester_name'   => (string) $this->request->getPost('requester_name'),
            'requester_phone'  => (string) $this->request->getPost('requester_phone') ?: null,
            'requester_email'  => (string) $this->request->getPost('requester_email') ?: null,
            'requester_org'    => (string) $this->request->getPost('requester_org') ?: null,
            'requester_note'   => (string) $this->request->getPost('requester_note') ?: null,
            'requested_at'     => date('Y-m-d H:i:s'),
            'approved_by'      => (int) session()->get('user_id'),
            'approved_at'      => date('Y-m-d H:i:s'),
            'status'           => 'Disetujui',
            'note'             => (string) $this->request->getPost('note') ?: 'Permintaan scan manual oleh admin.',
        ], true);

        $this->histories->insert([
            'loan_id'    => (int) $loanId,
            'status'     => 'Disetujui',
            'changed_by' => (int) session()->get('user_id'),
            'changed_at' => date('Y-m-d H:i:s'),
            'note'       => 'Permintaan scan manual oleh admin.',
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Permintaan scan manual gagal disimpan.');
        }
        $this->logActivity('create', 'Permintaan Scan', 'Menambahkan permintaan scan manual untuk BPKB ' . ($item['plate_number'] ?? '-') . '.', 'loans', (int) $loanId);

        return redirect()->to(site_url('admin/loans'))->with('success', 'Permintaan scan manual berhasil ditambahkan.');
    }

    public function approve(int $id)
    {
        $loan = $this->loans->find($id);
        if (! $loan) {
            return redirect()->to(site_url('admin/loans'))->with('error', 'Data permintaan scan tidak ditemukan.');
        }

        if ($loan['status'] !== 'Menunggu') {
            return redirect()->to(site_url('admin/loans'))->with('error', 'Status permintaan scan sudah diproses.');
        }

        $this->loans->update($id, [
            'status'      => 'Disetujui',
            'approved_by' => (int) session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        $this->histories->insert([
            'loan_id'    => $id,
            'status'     => 'Disetujui',
            'changed_by' => (int) session()->get('user_id'),
            'changed_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logActivity('approve', 'Permintaan Scan', 'Menyetujui permintaan scan BPKB ID ' . $loan['bpkb_id'] . '.', 'loans', $id);

        return redirect()->to(site_url('admin/loans'))->with('success', 'Permintaan Scan disetujui.');
    }

    public function reject(int $id)
    {
        $loan = $this->loans->find($id);
        if (! $loan) {
            return redirect()->to(site_url('admin/loans'))->with('error', 'Data permintaan scan tidak ditemukan.');
        }

        if ($loan['status'] !== 'Menunggu') {
            return redirect()->to(site_url('admin/loans'))->with('error', 'Status permintaan scan sudah diproses.');
        }

        $note = (string) $this->request->getPost('note');

        $this->loans->update($id, [
            'status'      => 'Ditolak',
            'approved_by' => (int) session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
            'note'        => $note !== '' ? $note : null,
        ]);

        $this->histories->insert([
            'loan_id'    => $id,
            'status'     => 'Ditolak',
            'changed_by' => (int) session()->get('user_id'),
            'changed_at' => date('Y-m-d H:i:s'),
            'note'       => $note !== '' ? $note : null,
        ]);
        $this->logActivity('reject', 'Permintaan Scan', 'Menolak permintaan scan BPKB ID ' . $loan['bpkb_id'] . '.', 'loans', $id);

        return redirect()->to(site_url('admin/loans'))->with('success', 'Permintaan Scan ditolak.');
    }

    public function delete(int $id)
    {
        $loan = $this->loans->find($id);
        if (! $loan) {
            return redirect()->back()->with('error', 'Data permintaan scan tidak ditemukan.');
        }

        $requestedAt = ! empty($loan['requested_at']) ? strtotime((string) $loan['requested_at']) : false;
        if (! $requestedAt || $requestedAt > strtotime('-7 days')) {
            return redirect()->back()->with('error', 'Permintaan scan hanya bisa dihapus setelah lebih dari 7 hari.');
        }

        $scanLabel = 'permintaan scan';
        if (! empty($loan['bpkb_id'])) {
            $bpkb = $this->bpkb->find((int) $loan['bpkb_id']);
            $scanLabel = 'permintaan scan BPKB ' . (string) ($bpkb['plate_number'] ?? 'ID ' . $loan['bpkb_id']);
        } elseif (! empty($loan['sertifikat_id'])) {
            $scanLabel = 'permintaan scan Sertipikat Tanah ' . (string) ($loan['no_sertipikat'] ?? 'ID ' . $loan['sertifikat_id']);
        }

        $db = db_connect();
        $db->transStart();

        $this->histories->where('loan_id', $id)->delete();
        $this->loans->delete($id);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Permintaan scan gagal dihapus.');
        }

        $this->logActivity('delete', 'Permintaan Scan', 'Menghapus ' . $scanLabel . ' yang lebih dari 7 hari.', 'loans', $id);

        return redirect()->back()->with('success', 'Permintaan scan berhasil dihapus.');
    }

    public function download(int $id)
    {
        $loan = $this->loans->find($id);
        if (! $loan) {
            return redirect()->back()->with('error', 'Data permintaan scan tidak ditemukan.');
        }

        if (($loan['status'] ?? '') !== 'Disetujui') {
            return redirect()->back()->with('error', 'File scan hanya bisa didownload setelah permintaan disetujui.');
        }

        $document = null;
        $filename = 'scan-' . $id . '.pdf';

        if (! empty($loan['bpkb_id'])) {
            $document = $this->bpkb->find((int) $loan['bpkb_id']);
            $filename = 'scan-bpkb-' . $this->filenameToken((string) ($document['plate_number'] ?? $loan['bpkb_id'])) . '.pdf';
        } elseif ($this->loanFieldExists('sertifikat_id') && ! empty($loan['sertifikat_id'])) {
            $document = $this->sertifikat->find((int) $loan['sertifikat_id']);
            $filename = 'scan-sertipikat-' . $this->filenameToken((string) ($document['no_sertipikat'] ?? $loan['sertifikat_id'])) . '.pdf';
        }

        if (! $document || empty($document['pdf_path'])) {
            return redirect()->back()->with('error', 'File scan untuk data yang diminta belum tersedia.');
        }

        $path = WRITEPATH . $document['pdf_path'];
        if (! is_file($path)) {
            return redirect()->back()->with('error', 'File scan untuk data yang diminta tidak ditemukan.');
        }

        $watermarkedPath = $this->watermarkPdf($path, $loan);
        if ($watermarkedPath === null) {
            return redirect()->back()->with('error', 'File scan gagal diberi watermark.');
        }

        $contents = file_get_contents($watermarkedPath);
        @unlink($watermarkedPath);

        if ($contents === false) {
            return redirect()->back()->with('error', 'File scan gagal dibaca.');
        }

        $this->logActivity('download', 'Permintaan Scan', 'Mendownload file scan permintaan ID ' . $id . '.', 'loans', $id);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($contents);
    }

    private function loanFieldExists(string $field): bool
    {
        return db_connect()->fieldExists($field, 'loans');
    }

    private function filenameToken(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'dokumen';
    }

    private function watermarkPdf(string $sourcePath, array $loan): ?string
    {
        $gsPath = trim((string) shell_exec('command -v gs 2>/dev/null'));
        if ($gsPath === '' || ! is_executable($gsPath)) {
            return null;
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'scan-watermark-');
        $scriptPath = tempnam(sys_get_temp_dir(), 'scan-watermark-ps-');
        if ($outputPath === false || $scriptPath === false) {
            return null;
        }

        $requesterName = trim((string) ($loan['requester_name'] ?? 'Pemohon'));
        if ($requesterName === '') {
            $requesterName = 'Pemohon';
        }

        $requestedDate = '-';
        if (! empty($loan['requested_at'])) {
            $timestamp = strtotime((string) $loan['requested_at']);
            $requestedDate = $timestamp !== false ? date('d/m/Y H:i', $timestamp) : (string) $loan['requested_at'];
        }

        $lineOne = 'DIMINTA OLEH: ' . strtoupper($requesterName);
        $lineTwo = 'TANGGAL PENGAJUAN: ' . $requestedDate;

        $script = <<<PS
<<
  /EndPage {
    2 ne {
      pop
      gsave
        0 setgray
        /Helvetica-Bold findfont 9 scalefont setfont
        24 24 moveto ({$this->pdfString($lineOne)}) show
        /Helvetica findfont 8 scalefont setfont
        24 13 moveto ({$this->pdfString($lineTwo)}) show
      grestore
      true
    } {
      pop
      false
    } ifelse
  } bind
>> setpagedevice
PS;

        if (file_put_contents($scriptPath, $script) === false) {
            @unlink($outputPath);
            @unlink($scriptPath);
            return null;
        }

        $command = escapeshellarg($gsPath)
            . ' -q -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -dCompatibilityLevel=1.4'
            . ' -sOutputFile=' . escapeshellarg($outputPath)
            . ' ' . escapeshellarg($scriptPath)
            . ' ' . escapeshellarg($sourcePath)
            . ' 2>&1';

        exec($command, $output, $exitCode);
        @unlink($scriptPath);

        if ($exitCode !== 0 || ! is_file($outputPath) || filesize($outputPath) === 0) {
            @unlink($outputPath);
            return null;
        }

        return $outputPath;
    }

    private function pdfString(string $value): string
    {
        $value = str_replace(["\r", "\n"], ' ', $value);
        $value = preg_replace('/[^\x20-\x7E]/', '?', $value) ?? '';

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }

}
