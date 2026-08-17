<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Detail Surat Penyerahan | ArsipKu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Detail Data Surat Penyerahan</h1>
        <div>
            <a href="<?= site_url('admin/surat-penyerahan/' . $item['id'] . '/edit') ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="<?= site_url('admin/surat-penyerahan') ?>" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">No. Surat</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['no_surat'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">NIBAR</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['nibar'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Status Penggunaan</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['status_penggunaan'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Spesifikasi</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['spesifikasi'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Jenis Penyerahan</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['jenis_penyerahan'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Luas</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['luas'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Tanggal Sertifikat</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['tanggal_perolehan'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Alamat</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['alamat'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Lokasi</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['lokasi'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Dinas</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['dinas'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Pemberi Hibah</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['pemberi_hibah'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Dokumen PDF</dt>
                    <dd class="col-sm-9">
                        <?php if (! empty($item['pdf_path'])): ?>
                            <a href="<?= site_url('admin/surat-penyerahan/' . $item['id'] . '/pdf') ?>" target="_blank" class="btn btn-sm btn-info" rel="noopener">
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
