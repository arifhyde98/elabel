<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Detail Box Surat Penyerahan | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1>Detail Box <?= esc((string) $box['box_code']) ?></h1>
            <div class="text-muted">Lokasi: <?= esc((string) ($box['lokasi'] ?? '-')) ?></div>
            <div class="text-muted">Total Surat: <?= esc((string) count($items)) ?> / <?= esc((string) $maxPerBox) ?></div>
        </div>
        <div class="d-flex">
            <a href="<?= site_url('admin/surat-penyerahan-boxes') ?>" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="<?= site_url('admin/surat-penyerahan/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Surat
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
                    <div class="alert alert-secondary mb-0">Box ini belum memiliki data surat penyerahan untuk digabung.</div>
                <?php elseif (empty($mergeTargets)): ?>
                    <div class="alert alert-warning mb-0">
                        Belum ada box tujuan yang bisa digabung. Total isi box sumber dan tujuan harus maksimal <?= esc((string) $maxPerBox) ?> surat.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Penggabungan bisa dilakukan antar lokasi jika total isi box sumber dan tujuan maksimal <?= esc((string) $maxPerBox) ?> surat. Lokasi box tujuan akan digabung menjadi daftar seperti <strong>Sojol, Sojol Utara</strong>.
                    </div>
                    <form action="<?= site_url('admin/surat-penyerahan-boxes/' . $box['id'] . '/merge') ?>" method="post" onsubmit="return confirm('Gabungkan seluruh isi box ini ke box tujuan? Box sumber akan dihapus setelah berhasil.');">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="target_box_id">Box Tujuan</label>
                            <select name="target_box_id" id="target_box_id" class="form-control" required>
                                <option value="">Pilih box tujuan</option>
                                <?php foreach ($mergeTargets as $target): ?>
                                    <option value="<?= esc((string) $target['id']) ?>">
                                        <?= esc((string) $target['box_code']) ?> - <?= esc((string) ($target['lokasi'] ?? '-')) ?> - isi <?= esc((string) $target['surat_count']) ?> surat (total jadi <?= esc((string) $target['combined_count']) ?>)
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
            <div class="card-body">
                <h5 class="mb-3">Pisahkan Kembali Box</h5>
                <?php if (empty($splitOptions)): ?>
                    <div class="alert alert-secondary mb-0">Box ini belum memiliki gabungan lokasi yang bisa dipisahkan kembali.</div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Pilih satu lokasi dari box gabungan untuk dipisahkan ke box baru. Box baru akan memakai kode turunan seperti <strong>(2)</strong>, <strong>(3)</strong>, dan seterusnya.
                    </div>
                    <form action="<?= site_url('admin/surat-penyerahan-boxes/' . $box['id'] . '/split') ?>" method="post" onsubmit="return confirm('Pisahkan lokasi terpilih ke box baru?');">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="split_location">Lokasi yang Dipisahkan</label>
                            <select name="split_location" id="split_location" class="form-control" required>
                                <option value="">Pilih lokasi</option>
                                <?php foreach ($splitOptions as $option): ?>
                                    <option value="<?= esc((string) $option['label']) ?>">
                                        <?= esc((string) $option['label']) ?> - <?= esc((string) $option['count']) ?> surat
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fas fa-code-branch"></i> Pisahkan Box
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
                            <th>No. Surat</th>
                            <th>NIBAR</th>
                            <th>Status Penggunaan</th>
                            <th>Luas</th>
                            <th>Tahun</th>
                            <th>Pemberi Hibah</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data surat penyerahan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc((string) ($item['no_surat'] ?? '-')) ?></td>
                                <td><?= esc((string) ($item['nibar'] ?? '-')) ?></td>
                                <td><?= esc((string) ($item['status_penggunaan'] ?? '-')) ?></td>
                                <td><?= esc((string) ($item['luas'] ?? '-')) ?></td>
                                <td><?= esc((string) ($item['tahun'] ?? '-')) ?></td>
                                <td><?= esc((string) ($item['pemberi_hibah'] ?? '-')) ?></td>
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
