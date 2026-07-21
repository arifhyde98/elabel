<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Detail BPKB Keluar | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Detail BPKB Keluar</h1>
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
                <dl class="row mb-0">
                    <dt class="col-sm-4">No. Polisi</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['plate_number'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">No. BPKB</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['no_bpkb'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">No. Rangka</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['no_rangka'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">No. Mesin</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['no_mesin'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Merek</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['merek'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Tipe</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['tipe'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Isi Silinder</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['isi_silinder'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Warna</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['warna'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Tahun Pembuatan</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['year'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Jenis</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['vehicle_type'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Box</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['box_code'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Tanggal Hapus</dt>
                    <dd class="col-sm-8">
                        <?= ! empty($item['deleted_at']) ? esc(date('Y-m-d', strtotime((string) $item['deleted_at']))) : '-' ?>
                    </dd>

                    <dt class="col-sm-4">Alasan</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['reason'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Keterangan</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['reason_detail'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">User</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['deleted_name'] ?? '-')) ?></dd>

                    <dt class="col-sm-4">Dokumen Pendukung</dt>
                    <dd class="col-sm-8">
                        <?= ! empty($item['support_doc_path']) ? 'Tersedia' : 'Tidak ada' ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
