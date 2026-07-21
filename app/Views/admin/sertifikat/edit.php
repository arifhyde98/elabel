<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Edit Data Sertipikat | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Edit Data Sertipikat Tanah</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/sertifikat/' . $item['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?= view('admin/sertifikat/_form', ['item' => $item]) ?>
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('admin/sertifikat') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
