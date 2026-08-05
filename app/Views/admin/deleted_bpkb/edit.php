<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Edit BPKB Keluar | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Edit BPKB Keluar</h1>
        <a href="<?= site_url('admin/bpkb-deleted') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/bpkb-deleted/' . $item['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="plate_number">No. Polisi</label>
                            <input type="text" name="plate_number" id="plate_number" class="form-control" value="<?= esc((string) (old('plate_number') ?: ($item['plate_number'] ?? ''))) ?>" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="vehicle_type">Jenis</label>
                            <?php $selectedType = (string) (old('vehicle_type') ?: ($item['vehicle_type'] ?? '')); ?>
                            <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                <option value="">Pilih jenis</option>
                                <option value="R4" <?= $selectedType === 'R4' ? 'selected' : '' ?>>R4</option>
                                <option value="R2" <?= $selectedType === 'R2' ? 'selected' : '' ?>>R2</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="year">Tahun Pembuatan</label>
                            <input type="number" name="year" id="year" class="form-control" value="<?= esc((string) (old('year') ?: ($item['year'] ?? ''))) ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="box_code">Box</label>
                            <input type="text" name="box_code" id="box_code" class="form-control" value="<?= esc((string) (old('box_code') ?: ($item['box_code'] ?? ''))) ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="no_bpkb">No. BPKB</label>
                            <input type="text" name="no_bpkb" id="no_bpkb" class="form-control" value="<?= esc((string) (old('no_bpkb') ?: ($item['no_bpkb'] ?? ''))) ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="nibar">NIBAR</label>
                            <input type="text" name="nibar" id="nibar" class="form-control" value="<?= esc((string) (old('nibar') ?: ($item['nibar'] ?? ''))) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="no_rangka">No. Rangka</label>
                            <input type="text" name="no_rangka" id="no_rangka" class="form-control" value="<?= esc((string) (old('no_rangka') ?: ($item['no_rangka'] ?? ''))) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="no_mesin">No. Mesin</label>
                            <input type="text" name="no_mesin" id="no_mesin" class="form-control" value="<?= esc((string) (old('no_mesin') ?: ($item['no_mesin'] ?? ''))) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="merek">Merek</label>
                            <input type="text" name="merek" id="merek" class="form-control" value="<?= esc((string) (old('merek') ?: ($item['merek'] ?? ''))) ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="tipe">Tipe</label>
                            <input type="text" name="tipe" id="tipe" class="form-control" value="<?= esc((string) (old('tipe') ?: ($item['tipe'] ?? ''))) ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="isi_silinder">Isi Silinder</label>
                            <input type="text" name="isi_silinder" id="isi_silinder" class="form-control" value="<?= esc((string) (old('isi_silinder') ?: ($item['isi_silinder'] ?? ''))) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="warna">Warna</label>
                            <input type="text" name="warna" id="warna" class="form-control" value="<?= esc((string) (old('warna') ?: ($item['warna'] ?? ''))) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="pengguna">Pengguna</label>
                            <input type="text" name="pengguna" id="pengguna" class="form-control" value="<?= esc((string) (old('pengguna') ?: ($item['pengguna'] ?? ''))) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reason">Alasan Keluar</label>
                        <?php $selectedReason = (string) (old('reason') ?: ($item['reason'] ?? '')); ?>
                        <select name="reason" id="reason" class="form-control" required>
                            <option value="">Pilih alasan</option>
                            <option value="Di pinjam" <?= $selectedReason === 'Di pinjam' ? 'selected' : '' ?>>Di pinjam</option>
                            <option value="Penjualan" <?= $selectedReason === 'Penjualan' ? 'selected' : '' ?>>Penjualan</option>
                            <option value="Dihibahkan" <?= $selectedReason === 'Dihibahkan' ? 'selected' : '' ?>>Dihibahkan</option>
                            <option value="Kendaraan hilang" <?= $selectedReason === 'Kendaraan hilang' ? 'selected' : '' ?>>Kendaraan hilang</option>
                            <option value="Kendaraan tidak ditemukan" <?= $selectedReason === 'Kendaraan tidak ditemukan' ? 'selected' : '' ?>>Kendaraan tidak ditemukan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reason_detail">Keterangan</label>
                        <textarea name="reason_detail" id="reason_detail" class="form-control" rows="3"><?= esc((string) (old('reason_detail') ?: ($item['reason_detail'] ?? ''))) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Dokumen Pendukung Saat Ini</label>
                        <div>
                            <?php if (! empty($item['support_doc_path'])): ?>
                                <span class="badge badge-success">Tersedia</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak ada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="support_doc">Ganti Dokumen Pendukung</label>
                        <input type="file" name="support_doc" id="support_doc" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti dokumen. Format: PDF/JPG/PNG, maks 5MB.</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('admin/bpkb-deleted') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
