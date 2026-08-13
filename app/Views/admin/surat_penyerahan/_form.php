<?php
    $item = $item ?? [];
?>
<div class="form-group">
    <label for="nibar">NIBAR (Bila Ada)</label>
    <input type="text" name="nibar" id="nibar" class="form-control" value="<?= esc((string) (old('nibar') ?: ($item['nibar'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="no_surat">No. Surat</label>
    <input type="text" name="no_surat" id="no_surat" class="form-control" value="<?= esc((string) (old('no_surat') ?: ($item['no_surat'] ?? ''))) ?>" required>
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
    <label for="jenis_penyerahan">Jenis Penyerahan</label>
    <input type="text" name="jenis_penyerahan" id="jenis_penyerahan" class="form-control" value="<?= esc((string) (old('jenis_penyerahan') ?: ($item['jenis_penyerahan'] ?? ''))) ?>">
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
    <label for="lokasi">Lokasi</label>
    <input type="text" name="lokasi" id="lokasi" class="form-control" value="<?= esc((string) (old('lokasi') ?: ($item['lokasi'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="alamat">Alamat</label>
    <input type="text" name="alamat" id="alamat" class="form-control" value="<?= esc((string) (old('alamat') ?: ($item['alamat'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="dinas">Dinas</label>
    <input type="text" name="dinas" id="dinas" class="form-control" value="<?= esc((string) (old('dinas') ?: ($item['dinas'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="pemberi_hibah">Pemberi Hibah</label>
    <input type="text" name="pemberi_hibah" id="pemberi_hibah" class="form-control" value="<?= esc((string) (old('pemberi_hibah') ?: ($item['pemberi_hibah'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="pdf"><?= ! empty($item['pdf_path']) ? 'Ganti Dokumen PDF Surat Penyerahan' : 'Upload Dokumen PDF Surat Penyerahan' ?></label>
    <input type="file" name="pdf" id="pdf" class="form-control-file" accept="application/pdf">
    <?php if (! empty($item['pdf_path'])): ?>
        <small class="form-text text-muted">
            Dokumen saat ini:
            <a href="<?= site_url('admin/surat-penyerahan/' . $item['id'] . '/pdf') ?>" target="_blank" rel="noopener">
                <i class="fas fa-file-pdf"></i> Lihat PDF
            </a>
        </small>
    <?php endif; ?>
</div>
