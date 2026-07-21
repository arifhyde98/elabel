<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Detail Sertipikat | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Detail Data Sertipikat Tanah</h1>
        <div>
            <a href="<?= site_url('admin/sertifikat/' . $item['id'] . '/edit') ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="<?= site_url('admin/sertifikat') ?>" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">No. Sertipikat</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['no_sertipikat'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">NIBAR</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['nibar'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Status Penggunaan</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['status_penggunaan'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Spesifikasi</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['spesifikasi'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Luas</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['luas'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Tanggal Perolehan</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['tanggal_perolehan'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Nilai Perolehan</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['nilai_perolehan'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Nama Pemilik</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['nama_pemilik'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Cara Perolehan</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['cara_perolehan'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Alamat</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['alamat'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Lokasi</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['lokasi'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Dinas</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['dinas'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Dokumen PDF</dt>
                    <dd class="col-sm-9">
                        <?php if (! empty($item['pdf_path'])): ?>
                            <a href="<?= site_url('admin/sertifikat/' . $item['id'] . '/view') ?>" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View PDF
                            </a>
                        <?php else: ?>
                            <span class="badge badge-secondary">Tidak ada</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
