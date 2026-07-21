<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Tambah BPKB <?= esc((string) $vehicleLabel) ?> | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Tambah Data BPKB <?= esc((string) $vehicleLabel) ?></h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/bpkb') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="alert alert-info">
                        Box akan dipilih otomatis berdasarkan tahun dokumen dan kapasitas (maks 55 BPKB per box).
                    </div>
                    <div class="form-group">
                        <label for="vehicle_type">Jenis Kendaraan</label>
                        <?php if (! empty($vehicleType)): ?>
                            <input type="hidden" name="vehicle_type" value="<?= esc((string) $vehicleType) ?>">
                            <input type="text" class="form-control" value="<?= esc((string) $vehicleLabel) ?>" readonly>
                        <?php else: ?>
                            <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                <option value="">Pilih jenis</option>
                                <option value="R4" <?= old('vehicle_type') === 'R4' ? 'selected' : '' ?>>R4</option>
                                <option value="R2" <?= old('vehicle_type') === 'R2' ? 'selected' : '' ?>>R2</option>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="year">Tahun Pembuatan</label>
                        <?php if (empty($years)): ?>
                            <div class="alert alert-warning mb-0">
                                Belum ada tahun di data box. Tambahkan data box dulu.
                            </div>
                        <?php else: ?>
                            <select name="year" id="year" class="form-control" required>
                                <option value="">Pilih tahun</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?= esc((string) $year) ?>" <?= (string) $year === (string) old('year') ? 'selected' : '' ?>>
                                        <?= esc((string) $year) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="plate_number">No. Polisi</label>
                        <input type="text" name="plate_number" id="plate_number" class="form-control" value="<?= esc((string) old('plate_number')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="no_bpkb">No. BPKB</label>
                        <input type="text" name="no_bpkb" id="no_bpkb" class="form-control" value="<?= esc((string) old('no_bpkb')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="no_rangka">No. Rangka</label>
                        <input type="text" name="no_rangka" id="no_rangka" class="form-control" value="<?= esc((string) old('no_rangka')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="no_mesin">No. Mesin</label>
                        <input type="text" name="no_mesin" id="no_mesin" class="form-control" value="<?= esc((string) old('no_mesin')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="merek">Merek Kendaraan</label>
                        <input type="text" name="merek" id="merek" class="form-control" value="<?= esc((string) old('merek')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="tipe">Tipe Kendaraan</label>
                        <input type="text" name="tipe" id="tipe" class="form-control" value="<?= esc((string) old('tipe')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="isi_silinder">Isi Silinder</label>
                        <input type="text" name="isi_silinder" id="isi_silinder" class="form-control" value="<?= esc((string) old('isi_silinder')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="warna">Warna</label>
                        <input type="text" name="warna" id="warna" class="form-control" value="<?= esc((string) old('warna')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="pengguna">Pengguna Kendaraan</label>
                        <input type="text" name="pengguna" id="pengguna" class="form-control" value="<?= esc((string) old('pengguna')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="pdf">Upload PDF BPKB</label>
                        <input type="file" name="pdf" id="pdf" class="form-control-file" accept="application/pdf">
                        <small class="text-muted">Opsional, dapat ditambahkan nanti melalui edit data.</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= $vehicleRoute ? site_url('admin/bpkb/' . $vehicleRoute) : site_url('admin/bpkb') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary" <?= empty($years) ? 'disabled' : '' ?>>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
