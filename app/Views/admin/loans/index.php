<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Permintaan Scan <?= esc((string) ($documentLabel ?? 'BPKB')) ?> | ArsipKu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .scan-tabs {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .scan-tab {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.22rem;
        width: 95px;
        min-width: 95px;
        min-height: 54px;
        padding: 0.32rem 0.5rem;
        border-radius: 8px;
        border: 2px solid #cbd5e1;
        background: #fff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.62rem;
        text-transform: uppercase;
        transition: all 0.2s ease;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }
    .scan-tab i {
        font-size: 0.8rem;
    }
    .scan-tab.active,
    .scan-tab:hover {
        border-color: #3b82f6;
        color: #1d4ed8;
        box-shadow: 0 10px 22px rgba(59, 130, 246, 0.2);
    }
    .loan-actions {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
        align-items: center;
    }
</style>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Daftar Permintaan Scan <?= esc((string) ($documentLabel ?? 'BPKB')) ?></h1>
        <?php if (($documentType ?? 'bpkb') === 'bpkb'): ?>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-manual-loan">
                <i class="fas fa-plus"></i> <span class="btn-text">Permintaan Scan</span>
            </button>
        <?php endif; ?>
    </div>
    <div class="container-fluid mt-2">
        <div class="scan-tabs" role="group" aria-label="Filter jenis dokumen permintaan scan">
            <a href="<?= site_url('admin/loans/bpkb') ?>" class="scan-tab <?= ($documentType ?? 'bpkb') === 'bpkb' ? 'active' : '' ?>">
                <i class="fas fa-id-card"></i>
                BPKB
            </a>
            <a href="<?= site_url('admin/loans/sertifikat') ?>" class="scan-tab <?= ($documentType ?? '') === 'sertifikat' ? 'active' : '' ?>">
                <i class="fas fa-file-contract"></i>
                Sertipikat
            </a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <?php if (($documentType ?? 'bpkb') === 'sertifikat'): ?>
                            <tr>
                                <th>No</th>
                                <th>No. Sertipikat</th>
                                <th>Box</th>
                                <th>Lokasi</th>
                                <th>Pemohon</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>No</th>
                                <th>Nomor Plat</th>
                                <th>Box</th>
                                <th>Tahun</th>
                                <th>Pemohon</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Belum ada pengajuan <?= esc(strtolower((string) ($documentLabel ?? 'BPKB'))) ?>.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <?php
                                $isSertifikat = ($documentType ?? 'bpkb') === 'sertifikat';
                                $requestedAt = ! empty($item['requested_at']) ? strtotime((string) $item['requested_at']) : false;
                                $canDeleteOldRequest = $requestedAt && $requestedAt <= strtotime('-7 days');
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc((string) ($isSertifikat ? ($item['no_sertipikat'] ?? '-') : ($item['plate_number'] ?? '-'))) ?></td>
                                <td><?= esc((string) $item['box_code']) ?></td>
                                <td><?= esc((string) ($isSertifikat ? ($item['lokasi'] ?? '-') : ($item['bpkb_year'] ?? '-'))) ?></td>
                                <td>
                                    <div><?= esc((string) ($item['requester_name'] ?? '-')) ?></div>
                                    <?php if (! empty($item['requester_phone'])): ?>
                                        <small class="text-muted"><?= esc((string) $item['requester_phone']) ?></small>
                                    <?php endif; ?>
                                    <?php if (! empty($item['requester_org'])): ?>
                                        <div><small class="text-muted"><?= esc((string) $item['requester_org']) ?></small></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc((string) $item['requested_at']) ?></td>
                                <td><?= esc((string) $item['status']) ?></td>
                                <td><?= esc((string) ($item['note'] ?? '-')) ?></td>
                                <td>
                                    <div class="loan-actions">
                                    <?php if ($item['status'] === 'Menunggu'): ?>
                                        <form action="<?= site_url('admin/loans/' . $item['id'] . '/approve') ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success btn-xs">Setujui</button>
                                        </form>
                                        <form action="<?= site_url('admin/loans/' . $item['id'] . '/reject') ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="text" name="note" class="form-control form-control-sm d-inline-block" style="width: 140px;" placeholder="Catatan">
                                            <button type="submit" class="btn btn-danger btn-xs">Tolak</button>
                                        </form>
                                    <?php elseif ($item['status'] === 'Disetujui'): ?>
                                        <?php if (! empty($item['document_pdf_path'])): ?>
                                            <a href="<?= site_url('admin/loans/' . $item['id'] . '/download') ?>" class="btn btn-success btn-xs" aria-label="Download File Scan" title="Download File Scan">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge badge-warning">File belum tersedia</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Selesai</span>
                                    <?php endif; ?>
                                    <?php if ($canDeleteOldRequest): ?>
                                        <form action="<?= site_url('admin/loans/' . $item['id'] . '/delete') ?>" method="post" class="mt-1" onsubmit="return confirm('Hapus permintaan scan ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-xs" aria-label="Hapus permintaan scan" title="Hapus permintaan scan">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modal-manual-loan" tabindex="-1" role="dialog" aria-labelledby="modalManualLoanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= site_url('admin/loans/manual') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalManualLoanLabel">Permintaan Scan Manual</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if (empty($availableBpkb)): ?>
                    <div class="alert alert-info mb-0">
                        Tidak ada BPKB berstatus tersedia untuk diminta file scan.
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="manual_bpkb_id">BPKB</label>
                        <select name="bpkb_id" id="manual_bpkb_id" class="custom-select" required>
                            <option value="">Pilih BPKB</option>
                            <?php foreach ($availableBpkb as $bpkb): ?>
                                <option value="<?= (int) $bpkb['id'] ?>" <?= old('bpkb_id') == $bpkb['id'] ? 'selected' : '' ?>>
                                    <?= esc((string) $bpkb['plate_number']) ?>
                                    - Box <?= esc((string) $bpkb['box_code']) ?>
                                    - <?= esc((string) $bpkb['year']) ?>
                                    <?= ! empty($bpkb['no_bpkb']) ? ' - No. BPKB ' . esc((string) $bpkb['no_bpkb']) : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual_requester_name">Nama Pemohon</label>
                                <input type="text" name="requester_name" id="manual_requester_name" class="form-control" value="<?= old('requester_name') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual_requester_phone">No. HP</label>
                                <input type="text" name="requester_phone" id="manual_requester_phone" class="form-control" value="<?= old('requester_phone') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual_requester_email">Email</label>
                                <input type="email" name="requester_email" id="manual_requester_email" class="form-control" value="<?= old('requester_email') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual_requester_org">Instansi</label>
                                <input type="text" name="requester_org" id="manual_requester_org" class="form-control" value="<?= old('requester_org') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="manual_requester_note">Keperluan</label>
                        <textarea name="requester_note" id="manual_requester_note" class="form-control" rows="2"><?= old('requester_note') ?></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label for="manual_note">Catatan Admin</label>
                        <textarea name="note" id="manual_note" class="form-control" rows="2"><?= old('note') ?></textarea>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" <?= empty($availableBpkb) ? 'disabled' : '' ?>>Simpan Permintaan Scan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
