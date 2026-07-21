<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Edit Data Surat Penyerahan | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Edit Data Surat Penyerahan</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/surat-penyerahan/' . $item['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <?= view('admin/surat_penyerahan/_form', ['item' => $item]) ?>
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('admin/surat-penyerahan') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
