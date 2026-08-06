<?php

namespace App\Controllers;

use App\Models\LoanModel;

class PublicInfoController extends BaseController
{
    public function policy(): string
    {
        return view('public/policy');
    }

    public function loanStatus(): string
    {
        $code = strtoupper(trim((string) $this->request->getGet('code')));
        $item = null;
        $error = null;

        if ($code !== '') {
            $loanId = $this->loanIdFromCode($code);
            if ($loanId === null) {
                $error = 'Format nomor pengajuan tidak valid.';
            } else {
                $item = (new LoanModel())
                    ->select('loans.*, bpkb.plate_number, bpkb.year as bpkb_year, boxes.box_code')
                    ->join('bpkb', 'bpkb.id = loans.bpkb_id')
                    ->join('boxes', 'boxes.id = bpkb.box_id')
                    ->where('loans.id', $loanId)
                    ->first();

                if (! $item) {
                    $error = 'Nomor pengajuan tidak ditemukan.';
                }
            }
        }

        return view('public/loan_status', [
            'code'  => $code,
            'item'  => $item,
            'error' => $error,
        ]);
    }

    private function loanIdFromCode(string $code): ?int
    {
        if (! preg_match('/^L-\d{8}-(\d{6,})$/', $code, $matches)) {
            return null;
        }

        $loanId = (int) ltrim($matches[1], '0');

        return $loanId > 0 ? $loanId : null;
    }
}
