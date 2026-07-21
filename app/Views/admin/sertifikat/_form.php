<?php
    $item = $item ?? [];
?>
<div class="form-group">
    <label for="no_sertipikat">No. Sertipikat</label>
    <input type="text" name="no_sertipikat" id="no_sertipikat" class="form-control" value="<?= esc((string) (old('no_sertipikat') ?: ($item['no_sertipikat'] ?? ''))) ?>" required>
</div>
<div class="form-group">
    <label for="nibar">NIBAR</label>
    <input type="text" name="nibar" id="nibar" class="form-control" value="<?= esc((string) (old('nibar') ?: ($item['nibar'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="status_penggunaan">Status Penggunaan</label>
    <input type="text" name="status_penggunaan" id="status_penggunaan" class="form-control" value="<?= esc((string) (old('status_penggunaan') ?: ($item['status_penggunaan'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="spesifikasi">Spesifikasi</label>
    <input type="text" name="spesifikasi" id="spesifikasi" class="form-control" value="<?= esc((string) (old('spesifikasi') ?: ($item['spesifikasi'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="luas">Luas</label>
    <input type="number" step="0.01" name="luas" id="luas" class="form-control" value="<?= esc((string) (old('luas') ?: ($item['luas'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="tanggal_perolehan">Tanggal Perolehan</label>
    <input type="date" name="tanggal_perolehan" id="tanggal_perolehan" class="form-control" value="<?= esc((string) (old('tanggal_perolehan') ?: ($item['tanggal_perolehan'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="nilai_perolehan">Nilai Perolehan</label>
    <input type="number" step="0.01" name="nilai_perolehan" id="nilai_perolehan" class="form-control" value="<?= esc((string) (old('nilai_perolehan') ?: ($item['nilai_perolehan'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="nama_pemilik">Nama Pemilik</label>
    <input type="text" name="nama_pemilik" id="nama_pemilik" class="form-control" value="<?= esc((string) (old('nama_pemilik') ?: ($item['nama_pemilik'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="cara_perolehan">Cara Perolehan</label>
    <input type="text" name="cara_perolehan" id="cara_perolehan" class="form-control" value="<?= esc((string) (old('cara_perolehan') ?: ($item['cara_perolehan'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="alamat">Alamat</label>
    <textarea name="alamat" id="alamat" class="form-control" rows="2"><?= esc((string) (old('alamat') ?: ($item['alamat'] ?? ''))) ?></textarea>
</div>
<div class="form-group">
    <label for="lokasi">Lokasi</label>
    <input type="text" name="lokasi" id="lokasi" class="form-control" value="<?= esc((string) (old('lokasi') ?: ($item['lokasi'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="dinas">Dinas</label>
    <input type="text" name="dinas" id="dinas" class="form-control" value="<?= esc((string) (old('dinas') ?: ($item['dinas'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="pdf">Upload Dokumen PDF Sertipikat</label>
    <input type="file" name="pdf" id="pdf" class="form-control-file" accept="application/pdf">
    <?php if (! empty($item['pdf_path'])): ?>
        <small class="form-text text-muted">
            File saat ini:
            <a href="<?= site_url('admin/sertifikat/' . $item['id'] . '/view') ?>" target="_blank">Lihat PDF</a>
        </small>
    <?php endif; ?>
</div>
