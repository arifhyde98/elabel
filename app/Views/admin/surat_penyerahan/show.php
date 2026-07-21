<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Detail Surat Penyerahan | eLabel<?= $this->endSection() ?>

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
                    <dt class="col-sm-3">NIBAR</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['nibar'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">No. Surat</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['no_surat'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Status Penggunaan</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['status_penggunaan'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Luas</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['luas'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Tahun</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['tahun'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Lokasi</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['lokasi'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Pemberi Hibah</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['pemberi_hibah'] ?? '-')) ?></dd>
                </dl>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
