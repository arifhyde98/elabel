<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Tambah Data Sertipikat | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Tambah Data Sertipikat Tanah</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/sertifikat') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?= view('admin/sertifikat/_form') ?>
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('admin/sertifikat') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
