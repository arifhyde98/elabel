<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Detail Box | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1>Detail Box <?= esc((string) $box['box_code']) ?></h1>
            <?php if (! empty($box['location'])): ?>
                <small class="text-muted">Lokasi: <?= esc((string) $box['location']) ?></small>
            <?php endif; ?>
            <?php if (! empty($years)): ?>
                <div class="text-muted">Tahun: <?= esc(implode(', ', array_map(static fn ($row) => $row['year'], $years))) ?></div>
            <?php endif; ?>
            <div class="text-muted">Total BPKB aktif: <?= esc((string) $mergeCandidateCount) ?></div>
        </div>
        <div class="d-flex">
            <a href="<?= site_url('admin/boxes') ?>" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="<?= site_url('admin/bpkb/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah BPKB
            </a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Gabungkan Box</h5>
                <?php if ($mergeCandidateCount === 0): ?>
                    <div class="alert alert-secondary mb-0">Box ini belum memiliki data BPKB untuk digabung.</div>
                <?php elseif ($mergeCandidateCount > $maxMergeSource): ?>
                    <div class="alert alert-warning mb-0">
                        Box hanya bisa digabung ke box lain jika berisi maksimal <?= esc((string) $maxMergeSource) ?> BPKB. Saat ini box berisi <?= esc((string) $mergeCandidateCount) ?> BPKB.
                    </div>
                <?php elseif (empty($mergeTargets)): ?>
                    <div class="alert alert-warning mb-0">
                        Belum ada box tujuan yang kompatibel untuk menerima penggabungan.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Gabungkan box ini ke box lain dengan jenis kendaraan yang sama dan tahun box sumber harus sudah tercakup di box tujuan. Khusus hasil penggabungan, kapasitas box tujuan boleh melebihi 55 BPKB.
                    </div>
                    <form action="<?= site_url('admin/boxes/' . $box['id'] . '/merge') ?>" method="post" onsubmit="return confirm('Gabungkan seluruh data box ini ke box tujuan? Box sumber akan dihapus setelah berhasil.');">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="target_box_id">Box Tujuan</label>
                            <select name="target_box_id" id="target_box_id" class="form-control" required>
                                <option value="">Pilih box tujuan</option>
                                <?php foreach ($mergeTargets as $target): ?>
                                    <option value="<?= esc((string) $target['id']) ?>">
                                        <?= esc((string) $target['box_code']) ?> - isi <?= esc((string) $target['bpkb_count']) ?> BPKB
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-object-group"></i> Gabungkan Box
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun</th>
                            <th>No. Polisi</th>
                            <th>Status</th>
                            <th>File PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data BPKB.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc((string) $item['year']) ?></td>
                                <td><?= esc((string) $item['plate_number']) ?></td>
                                <td><?= esc((string) $item['status']) ?></td>
                                <td>
                                    <?php if (! empty($item['pdf_path'])): ?>
                                        <span class="badge badge-success">Ada</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Tidak ada</span>
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
<?= $this->endSection() ?>
