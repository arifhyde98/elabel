<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Models\BpkbDeleteModel;
use App\Models\BpkbModel;
use App\Models\BoxModel;
use App\Models\LoanModel;
use App\Models\SertifikatBoxModel;
use App\Models\SertifikatModel;

class DashboardController extends BaseController
{
    private const ACTIVITY_RETENTION_DAYS = 180;

    public function index(): string
    {
        $boxModel = new BoxModel();
        $bpkbBoxCount = $boxModel->countAllResults();
        $sertifikatBoxCount = (new SertifikatBoxModel())->countAllResults();
        $boxMobilCount = (clone $boxModel)
            ->where('vehicle_type', 'R4')
            ->countAllResults();
        $boxMotorCount = (clone $boxModel)
            ->where('vehicle_type', 'R2')
            ->countAllResults();
        $bpkbModel = new BpkbModel();
        $bpkbCount = $bpkbModel
            ->where('status !=', 'Dihapus')
            ->countAllResults();
        $bpkbMobilCount = (clone $bpkbModel)
            ->where('status !=', 'Dihapus')
            ->where('vehicle_type', 'R4')
            ->countAllResults();
        $bpkbMotorCount = (clone $bpkbModel)
            ->where('status !=', 'Dihapus')
            ->where('vehicle_type', 'R2')
            ->countAllResults();
        $bpkbAvailableCount = (clone $bpkbModel)
            ->where('status', 'Tersedia')
            ->countAllResults();
        $bpkbDeletedCount = (new BpkbDeleteModel())->countAllResults();
        $filledBoxCount = $boxModel->builder()
            ->select('boxes.id')
            ->join('bpkb', "bpkb.box_id = boxes.id AND bpkb.status != 'Dihapus'", 'inner')
            ->distinct()
            ->countAllResults();
        $loanModel = new LoanModel();
        $loanCount = $loanModel->countAllResults();
        $loanApprovedCount = (clone $loanModel)
            ->where('status', 'Disetujui')
            ->countAllResults();
        $sertifikatModel = new SertifikatModel();
        $sertifikatCount = $sertifikatModel->countAllResults();

        $db = db_connect();
        $suratPenyerahanCount = $db->tableExists('surat_penyerahan')
            ? $db->table('surat_penyerahan')->countAllResults()
            : 0;
        $suratPenyerahanBoxCount = $db->tableExists('surat_penyerahan_boxes')
            ? $db->table('surat_penyerahan_boxes')->countAllResults()
            : 0;
        $boxCount = $bpkbBoxCount + $sertifikatBoxCount + $suratPenyerahanBoxCount;

        $boxFilledPercent = $boxCount > 0 ? (int) round(($filledBoxCount / $boxCount) * 100) : 0;
        $bpkbActivePercent = $bpkbCount > 0 ? (int) round(($bpkbAvailableCount / $bpkbCount) * 100) : 0;
        $loanPercent = $bpkbCount > 0 ? min(100, (int) round(($loanCount / $bpkbCount) * 100)) : 0;
        $activityLogs = [];
        $oldActivity180Count = 0;
        if ($db->tableExists('activity_logs')) {
            $oldActivity180Count = (new ActivityLogModel())
                ->where('created_at <', date('Y-m-d H:i:s', strtotime('-' . self::ACTIVITY_RETENTION_DAYS . ' days')))
                ->countAllResults();
            $activityLogs = (new ActivityLogModel())
                ->select('activity_logs.*, users.name as user_name, users.email as user_email')
                ->join('users', 'users.id = activity_logs.user_id', 'left')
                ->orderBy('activity_logs.created_at', 'desc')
                ->limit(10)
                ->findAll();
        }

        return view('admin/dashboard', [
            'name'       => session()->get('user_name'),
            'email'      => session()->get('user_email'),
            'role'       => session()->get('user_role'),
            'boxCount'       => $boxCount,
            'bpkbBoxCount' => $bpkbBoxCount,
            'sertifikatBoxCount' => $sertifikatBoxCount,
            'suratPenyerahanBoxCount' => $suratPenyerahanBoxCount,
            'boxMobilCount'  => $boxMobilCount,
            'boxMotorCount'  => $boxMotorCount,
            'bpkbCount'      => $bpkbCount,
            'bpkbMobilCount' => $bpkbMobilCount,
            'bpkbMotorCount' => $bpkbMotorCount,
            'bpkbDeletedCount' => $bpkbDeletedCount,
            'filledBoxCount' => $filledBoxCount,
            'bpkbAvailableCount' => $bpkbAvailableCount,
            'loanCount' => $loanCount,
            'loanApprovedCount' => $loanApprovedCount,
            'sertifikatCount' => $sertifikatCount,
            'suratPenyerahanCount' => $suratPenyerahanCount,
            'boxFilledPercent' => $boxFilledPercent,
            'bpkbActivePercent' => $bpkbActivePercent,
            'loanPercent' => $loanPercent,
            'activityLogs' => $activityLogs,
            'activityRetentionDays' => self::ACTIVITY_RETENTION_DAYS,
            'oldActivity180Count' => $oldActivity180Count,
            'activeMenu' => 'dashboard',
        ]);
    }

    public function cleanupActivityLogs()
    {
        if (session()->get('user_role') !== 'super_admin') {
            return redirect()->to(site_url('admin'))->with('error', 'Hanya super admin yang dapat membersihkan riwayat aktifitas.');
        }

        $db = db_connect();
        if (! $db->tableExists('activity_logs')) {
            return redirect()->to(site_url('admin'))->with('error', 'Tabel riwayat aktifitas belum tersedia.');
        }

        $cutoff = date('Y-m-d H:i:s', strtotime('-' . self::ACTIVITY_RETENTION_DAYS . ' days'));
        $activityLogModel = new ActivityLogModel();
        $deletedCount = $activityLogModel
            ->where('created_at <', $cutoff)
            ->countAllResults();

        if ($deletedCount > 0) {
            $activityLogModel
                ->where('created_at <', $cutoff)
                ->delete();
        }

        $this->logActivity(
            'delete',
            'Riwayat Aktifitas',
            'Membersihkan ' . $deletedCount . ' riwayat aktifitas lebih lama dari ' . self::ACTIVITY_RETENTION_DAYS . ' hari.',
            'activity_logs',
            null
        );

        return redirect()->to(site_url('admin'))->with('success', $deletedCount . ' riwayat aktifitas lama berhasil dibersihkan.');
    }
}
