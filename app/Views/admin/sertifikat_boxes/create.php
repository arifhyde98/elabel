<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Tambah Box Sertipikat | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Tambah Box Sertipikat Tanah</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/sertifikat-boxes') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="box_code">Kode Box</label>
                        <input type="text" name="box_code" id="box_code" class="form-control" value="<?= esc((string) old('box_code', $nextBoxCode)) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lokasi">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" class="form-control" value="<?= esc((string) old('lokasi')) ?>" required>
                        <small class="text-muted">Jika lokasi sama, kode box berikutnya akan mengikuti kode dasar yang sama dengan suffix `(2)`, `(3)`, dan seterusnya.</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('admin/sertifikat-boxes') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
