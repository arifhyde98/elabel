<?php

namespace App\Controllers;

use App\Models\BpkbModel;
use App\Models\LoanHistoryModel;
use App\Models\LoanModel;

class LoanController extends BaseController
{
    private LoanModel $loans;
    private LoanHistoryModel $histories;
    private BpkbModel $bpkb;

    public function __construct()
    {
        $this->loans     = new LoanModel();
        $this->histories = new LoanHistoryModel();
        $this->bpkb      = new BpkbModel();
    }

    public function index(): string
    {
        $userId = (int) session()->get('user_id');
        $items  = $this->loans
            ->select('loans.*, bpkb.plate_number, bpkb.year as bpkb_year, boxes.box_code')
            ->join('bpkb', 'bpkb.id = loans.bpkb_id')
            ->join('boxes', 'boxes.id = bpkb.box_id')
            ->where('loans.requester_id', $userId)
            ->orderBy('loans.requested_at', 'desc')
            ->findAll();

        return view('user/loans/index', [
            'items'      => $items,
            'activeMenu' => 'loans',
        ]);
    }

    public function store()
    {
        $bpkbId = (int) $this->request->getPost('bpkb_id');
        $item   = $this->bpkb->find($bpkbId);

        if (! $item || $item['status'] !== 'Tersedia') {
            return redirect()->back()->with('error', 'BPKB tidak tersedia untuk dipinjam.');
        }

        $exists = $this->loans
            ->where('bpkb_id', $bpkbId)
            ->where('status', 'Menunggu')
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Pengajuan peminjaman sudah ada.');
        }

        $loanId = $this->loans->insert([
            'bpkb_id'      => $bpkbId,
            'requester_id' => (int) session()->get('user_id'),
            'requested_at' => date('Y-m-d H:i:s'),
            'status'       => 'Menunggu',
        ], true);

        $this->histories->insert([
            'loan_id'    => (int) $loanId,
            'status'     => 'Menunggu',
            'changed_by' => (int) session()->get('user_id'),
            'changed_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('loans'))->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    public function storePublic()
    {
        $returnUrl = $this->safeReturnUrl((string) $this->request->getPost('return_url'));
        $rules = [
            'bpkb_id'         => 'required|integer',
            'requester_name'  => 'required|min_length[3]|max_length[100]',
            'requester_phone' => 'required|min_length[8]|max_length[30]',
            'requester_email' => 'permit_empty|valid_email|max_length[150]',
            'requester_org'   => 'permit_empty|max_length[150]',
            'requester_address' => 'permit_empty|max_length[255]',
            'requester_note'  => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($returnUrl)->withInput()->with('errors', $this->validator->getErrors());
        }

        $bpkbId = (int) $this->request->getPost('bpkb_id');
        $item   = $this->bpkb->find($bpkbId);

        if (! $item || $item['status'] !== 'Tersedia') {
            return redirect()->to($returnUrl)->withInput()->with('error', 'BPKB tidak tersedia untuk dipinjam.');
        }

        $exists = $this->loans
            ->where('bpkb_id', $bpkbId)
            ->where('status', 'Menunggu')
            ->first();
        if ($exists) {
            return redirect()->to($returnUrl)->withInput()->with('error', 'Pengajuan peminjaman sudah ada.');
        }

        $db = db_connect();
        $db->transStart();

        $loanId = $this->loans->insert([
            'bpkb_id'          => $bpkbId,
            'requester_id'     => null,
            'requester_name'   => (string) $this->request->getPost('requester_name'),
            'requester_phone'  => (string) $this->request->getPost('requester_phone'),
            'requester_email'  => (string) $this->request->getPost('requester_email') ?: null,
            'requester_org'    => (string) $this->request->getPost('requester_org') ?: null,
            'requester_address'=> (string) $this->request->getPost('requester_address') ?: null,
            'requester_note'   => (string) $this->request->getPost('requester_note') ?: null,
            'requested_at'     => date('Y-m-d H:i:s'),
            'status'           => 'Menunggu',
        ], true);

        if ($loanId === false) {
            $db->transRollback();
            return redirect()->to($returnUrl)->withInput()->with('error', 'Pengajuan gagal disimpan. Silakan coba lagi.');
        }

        $this->histories->insert([
            'loan_id'    => (int) $loanId,
            'status'     => 'Menunggu',
            'changed_by' => null,
            'changed_at' => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to($returnUrl)->withInput()->with('error', 'Pengajuan gagal disimpan. Silakan coba lagi.');
        }

        $code = 'L-' . date('Ymd') . '-' . str_pad((string) $loanId, 6, '0', STR_PAD_LEFT);

        return redirect()
            ->to($returnUrl)
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.')
            ->with('success_detail', [
                'code' => $code,
                'name' => (string) $this->request->getPost('requester_name'),
                'phone' => (string) $this->request->getPost('requester_phone'),
            ]);
    }

    private function safeReturnUrl(string $returnUrl): string
    {
        $fallback = site_url('/');
        if ($returnUrl === '') {
            return $fallback;
        }

        $baseUrl = rtrim(site_url('/'), '/');
        if (strpos($returnUrl, $baseUrl) !== 0) {
            return $fallback;
        }

        return $returnUrl;
    }
}
