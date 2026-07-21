<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Detail BPKB | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Detail BPKB</h1>
        <div>
            <a href="<?= site_url('admin/bpkb/' . $item['id'] . '/edit') ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="<?= site_url('admin/bpkb') ?>" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Kode Box</dt>
                    <dd class="col-sm-9"><?= esc((string) $item['box_code']) ?></dd>

                    <dt class="col-sm-3">Lokasi Ruang</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['location'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Tahun Pembuatan</dt>
                    <dd class="col-sm-9"><?= esc((string) $item['year']) ?></dd>

                    <dt class="col-sm-3">No. Polisi</dt>
                    <dd class="col-sm-9"><?= esc((string) $item['plate_number']) ?></dd>

                    <dt class="col-sm-3">No. BPKB</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['no_bpkb'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">No. Rangka</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['no_rangka'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">No. Mesin</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['no_mesin'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Merek</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['merek'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Tipe</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['tipe'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Isi Silinder</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['isi_silinder'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Warna</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['warna'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Pengguna</dt>
                    <dd class="col-sm-9"><?= esc((string) ($item['pengguna'] ?? '-')) ?></dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9"><?= esc((string) $item['status']) ?></dd>

                    <dt class="col-sm-3">Tanggal Input</dt>
                    <dd class="col-sm-9"><?= esc((string) $item['created_at']) ?></dd>

                    <dt class="col-sm-3">User Penginput</dt>
                    <dd class="col-sm-9"><?= esc((string) $item['input_name']) ?></dd>

                    <dt class="col-sm-3">File PDF</dt>
                    <dd class="col-sm-9">
                        <?php if (! empty($item['pdf_path'])): ?>
                            <a href="<?= site_url('admin/bpkb/' . $item['id'] . '/view') ?>" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> <span class="btn-text">View PDF</span>
                            </a>
                        <?php else: ?>
                            <span class="badge badge-secondary">Tidak ada</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
