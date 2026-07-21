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
    <label for="luas">Luas</label>
    <input type="number" step="0.01" name="luas" id="luas" class="form-control" value="<?= esc((string) (old('luas') ?: ($item['luas'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="tahun">Tahun</label>
    <input type="number" name="tahun" id="tahun" class="form-control" min="1900" max="2100" value="<?= esc((string) (old('tahun') ?: ($item['tahun'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="lokasi">Lokasi</label>
    <input type="text" name="lokasi" id="lokasi" class="form-control" value="<?= esc((string) (old('lokasi') ?: ($item['lokasi'] ?? ''))) ?>">
</div>
<div class="form-group">
    <label for="pemberi_hibah">Pemberi Hibah</label>
    <input type="text" name="pemberi_hibah" id="pemberi_hibah" class="form-control" value="<?= esc((string) (old('pemberi_hibah') ?: ($item['pemberi_hibah'] ?? ''))) ?>">
</div>
