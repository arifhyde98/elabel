<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Peminjaman | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Daftar Peminjaman</h1>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-manual-loan">
            <i class="fas fa-plus"></i> <span class="btn-text">Peminjaman</span>
        </button>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
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
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Belum ada pengajuan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc((string) $item['plate_number']) ?></td>
                                <td><?= esc((string) $item['box_code']) ?></td>
                                <td><?= esc((string) $item['bpkb_year']) ?></td>
                                <td>
                                    <div><?= esc((string) $item['requester_name']) ?></div>
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
                                        <form action="<?= site_url('admin/loans/' . $item['id'] . '/return') ?>" method="post" class="d-inline" onsubmit="return confirm('Tandai dokumen sudah dikembalikan?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-primary btn-xs">Dikembalikan</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Selesai</span>
                                    <?php endif; ?>
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
                <h5 class="modal-title" id="modalManualLoanLabel">Peminjaman Manual</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if (empty($availableBpkb)): ?>
                    <div class="alert alert-info mb-0">
                        Tidak ada BPKB berstatus tersedia untuk dipinjam.
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
                                <label for="manual_requester_name">Nama Peminjam</label>
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
                <button type="submit" class="btn btn-primary" <?= empty($availableBpkb) ? 'disabled' : '' ?>>Simpan Peminjaman</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
