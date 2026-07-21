<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Edit BPKB <?= esc((string) $vehicleLabel) ?> | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Edit Data BPKB <?= esc((string) $vehicleLabel) ?></h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/bpkb/' . $item['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="alert alert-info">
                        Jika tahun atau jenis kendaraan diubah, box akan disesuaikan otomatis berdasarkan data box dan kapasitas.
                    </div>
                    <div class="form-group">
                        <label for="vehicle_type">Jenis Kendaraan</label>
                        <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                            <?php $selectedType = (string) (old('vehicle_type') ?: ($item['vehicle_type'] ?? '')); ?>
                            <option value="">Pilih jenis</option>
                            <option value="R4" <?= $selectedType === 'R4' ? 'selected' : '' ?>>R4</option>
                            <option value="R2" <?= $selectedType === 'R2' ? 'selected' : '' ?>>R2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="year">Tahun Pembuatan</label>
                        <?php $selectedYear = (string) (old('year') ?: ($item['year'] ?? '')); ?>
                        <?php if (empty($years)): ?>
                            <div class="alert alert-warning mb-0">
                                Belum ada tahun di data box. Tambahkan data box dulu.
                            </div>
                        <?php else: ?>
                            <select name="year" id="year" class="form-control" required>
                                <option value="">Pilih tahun</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?= esc((string) $year) ?>" <?= (string) $year === $selectedYear ? 'selected' : '' ?>>
                                        <?= esc((string) $year) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="plate_number">No. Polisi</label>
                        <input type="text" name="plate_number" id="plate_number" class="form-control" value="<?= esc((string) (old('plate_number') ?: ($item['plate_number'] ?? ''))) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="no_bpkb">No. BPKB</label>
                        <input type="text" name="no_bpkb" id="no_bpkb" class="form-control" value="<?= esc((string) (old('no_bpkb') ?: ($item['no_bpkb'] ?? ''))) ?>">
                    </div>
                    <div class="form-group">
                        <label for="no_rangka">No. Rangka</label>
                        <input type="text" name="no_rangka" id="no_rangka" class="form-control" value="<?= esc((string) (old('no_rangka') ?: ($item['no_rangka'] ?? ''))) ?>">
                    </div>
                    <div class="form-group">
                        <label for="no_mesin">No. Mesin</label>
                        <input type="text" name="no_mesin" id="no_mesin" class="form-control" value="<?= esc((string) (old('no_mesin') ?: ($item['no_mesin'] ?? ''))) ?>">
                    </div>
                    <div class="form-group">
                        <label for="merek">Merek Kendaraan</label>
                        <input type="text" name="merek" id="merek" class="form-control" value="<?= esc((string) (old('merek') ?: ($item['merek'] ?? ''))) ?>">
                    </div>
                    <div class="form-group">
                        <label for="tipe">Tipe Kendaraan</label>
                        <input type="text" name="tipe" id="tipe" class="form-control" value="<?= esc((string) (old('tipe') ?: ($item['tipe'] ?? ''))) ?>">
                    </div>
                    <div class="form-group">
                        <label for="isi_silinder">Isi Silinder</label>
                        <input type="text" name="isi_silinder" id="isi_silinder" class="form-control" value="<?= esc((string) (old('isi_silinder') ?: ($item['isi_silinder'] ?? ''))) ?>">
                    </div>
                    <div class="form-group">
                        <label for="warna">Warna</label>
                        <input type="text" name="warna" id="warna" class="form-control" value="<?= esc((string) (old('warna') ?: ($item['warna'] ?? ''))) ?>">
                    </div>
                    <div class="form-group">
                        <label for="pengguna">Pengguna Kendaraan</label>
                        <input type="text" name="pengguna" id="pengguna" class="form-control" value="<?= esc((string) (old('pengguna') ?: ($item['pengguna'] ?? ''))) ?>">
                    </div>
                    <div class="form-group">
                        <label>Box Saat Ini</label>
                        <input type="text" class="form-control" value="<?= esc((string) ($item['box_code'] ?? '-')) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>File PDF Saat Ini</label>
                        <div>
                            <?php if (! empty($item['pdf_path'])): ?>
                                <a href="<?= site_url('admin/bpkb/' . $item['id'] . '/view') ?>" target="_blank" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> <span class="btn-text">View PDF</span>
                                </a>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak ada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pdf">Ganti PDF BPKB</label>
                        <input type="file" name="pdf" id="pdf" class="form-control-file" accept="application/pdf">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti file PDF.</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= $vehicleRoute ? site_url('admin/bpkb/' . $vehicleRoute) : site_url('admin/bpkb') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary" <?= empty($years) ? 'disabled' : '' ?>>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
