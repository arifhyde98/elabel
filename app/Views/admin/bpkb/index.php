<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Data BPKB <?= esc((string) $vehicleLabel) ?> | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $shouldOpenModal = ! empty(old('plate_number'))
        || ! empty(old('year'))
        || ! empty(old('no_bpkb'))
        || ! empty(old('no_rangka'))
        || ! empty(old('no_mesin'))
        || ! empty(old('merek'))
        || ! empty(old('tipe'))
        || ! empty(old('isi_silinder'))
        || ! empty(old('warna'))
        || ! empty(old('pengguna'));
    $shouldOpenImportModal = session()->getFlashdata('openModal') === 'import';
?>
<style>
    #bpkb-table {
        width: 100% !important;
        table-layout: fixed;
    }
    #bpkb-table th,
    #bpkb-table td {
        vertical-align: top;
        font-size: 0.8rem;
        line-height: 1.2;
        padding: 0.38rem 0.45rem;
    }
    #bpkb-table th {
        font-size: 0.76rem;
        letter-spacing: 0.02em;
    }
    #bpkb-table .btn-xs {
        white-space: nowrap;
        font-size: 0.72rem;
        padding: 0.18rem 0.38rem;
    }
    .bpkb-col-no {
        width: 48px;
        min-width: 48px;
        text-align: center;
        white-space: nowrap;
    }
    .bpkb-col-plate {
        width: 108px;
    }
    .bpkb-col-bpkb {
        width: 126px;
    }
    .bpkb-col-rangka,
    .bpkb-col-mesin {
        width: 132px;
        word-break: break-word;
    }
    .bpkb-col-merek {
        width: 86px;
    }
    .bpkb-col-year {
        width: 82px;
        text-align: center;
        white-space: nowrap;
    }
    .bpkb-col-jenis {
        width: 62px;
        text-align: center;
        white-space: nowrap;
    }
    .bpkb-col-aksi {
        width: 110px;
        min-width: 110px;
        white-space: nowrap;
    }
    .vehicle-tabs {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .vehicle-tab {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.22rem;
        min-width: 56px;
        padding: 0.32rem 0.4rem;
        border-radius: 12px;
        border: 2px solid #cbd5e1;
        background: #fff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.62rem;
        text-transform: uppercase;
        transition: all 0.2s ease;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }
    .vehicle-tab i {
        font-size: 0.8rem;
    }
    .vehicle-tab.active,
    .vehicle-tab:hover {
        border-color: #3b82f6;
        color: #1d4ed8;
        box-shadow: 0 10px 22px rgba(59, 130, 246, 0.2);
    }
    @media (max-width: 767.98px) {
        .vehicle-tab {
            min-width: 52px;
            padding: 0.3rem 0.36rem;
        }
    }
</style>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Data BPKB <?= esc((string) $vehicleLabel) ?></h1>
        <div class="d-flex align-items-center">
            <a href="<?= site_url('admin/bpkb/export' . ($vehicleType ? '?type=' . $vehicleType : '')) ?>" class="btn btn-success btn-sm mr-2">
                <i class="fas fa-file-excel"></i> Download Excel
            </a>
            <button type="button" class="btn btn-secondary btn-sm mr-2" data-toggle="modal" data-target="#modal-import-bpkb">
                <i class="fas fa-file-upload"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-bpkb">
                <i class="fas fa-plus"></i> Tambah BPKB
            </button>
        </div>
    </div>
    <div class="container-fluid mt-2">
        <div class="vehicle-tabs" role="group" aria-label="Filter jenis kendaraan">
            <a href="<?= site_url('admin/bpkb') ?>" class="vehicle-tab <?= empty($vehicleType) ? 'active' : '' ?>">
                <i class="fas fa-layer-group"></i>
                Semua
            </a>
            <a href="<?= site_url('admin/bpkb/r4') ?>" class="vehicle-tab <?= ($vehicleType ?? '') === 'R4' ? 'active' : '' ?>">
                <i class="fas fa-car-side"></i>
                R4
            </a>
            <a href="<?= site_url('admin/bpkb/r2') ?>" class="vehicle-tab <?= ($vehicleType ?? '') === 'R2' ? 'active' : '' ?>">
                <i class="fas fa-motorcycle"></i>
                R2
            </a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table id="bpkb-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="bpkb-col-no">No</th>
                            <th class="bpkb-col-plate">No. Polisi</th>
                            <th class="bpkb-col-bpkb">No. BPKB</th>
                            <th class="bpkb-col-rangka">No. Rangka</th>
                            <th class="bpkb-col-mesin">No. Mesin</th>
                            <th class="bpkb-col-merek">Merek</th>
                            <th class="bpkb-col-year">Tahun Pembuatan</th>
                            <th class="bpkb-col-jenis">Jenis</th>
                            <th class="bpkb-col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="bpkb-col-no"><?= $i++ ?></td>
                                <td class="bpkb-col-plate"><?= esc((string) $item['plate_number']) ?></td>
                                <td class="bpkb-col-bpkb"><?= esc((string) ($item['no_bpkb'] ?? '-')) ?></td>
                                <td class="bpkb-col-rangka"><?= esc((string) ($item['no_rangka'] ?? '-')) ?></td>
                                <td class="bpkb-col-mesin"><?= esc((string) ($item['no_mesin'] ?? '-')) ?></td>
                                <td class="bpkb-col-merek"><?= esc((string) ($item['merek'] ?? '-')) ?></td>
                                <td class="bpkb-col-year"><?= esc((string) $item['year']) ?></td>
                                <td class="bpkb-col-jenis"><?= esc(strtoupper((string) ($item['vehicle_type'] ?? '-'))) ?></td>
                                <td class="bpkb-col-aksi">
                                    <a href="<?= site_url('admin/bpkb/' . $item['id'] . '/edit') ?>" class="btn btn-warning btn-xs">
                                        <i class="fas fa-edit"></i> <span class="btn-text">Edit</span>
                                    </a>
                                    <a href="<?= site_url('admin/bpkb/' . $item['id']) ?>" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i> <span class="btn-text">View</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modal-bpkb" tabindex="-1" role="dialog" aria-labelledby="modalBpkbLabel" aria-hidden="true">
    <div class="modal-dialog modal-compact modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= site_url('admin/bpkb') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBpkbLabel">Tambah Data BPKB <?= esc((string) $vehicleLabel) ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Box akan dipilih otomatis berdasarkan tahun dokumen dan kapasitas (maks 55 BPKB per box).
                    </div>
                    <div class="form-group floating-input">
                        <?php if (! empty($vehicleType)): ?>
                            <input type="hidden" name="vehicle_type" value="<?= esc((string) $vehicleType) ?>">
                            <input type="text" class="form-control" value="<?= esc((string) $vehicleLabel) ?>" readonly>
                            <label>Jenis kendaraan</label>
                        <?php else: ?>
                            <select name="vehicle_type" id="vehicle_type" class="custom-select" required>
                                <option value="" <?= old('vehicle_type') === '' ? 'selected' : '' ?>>Jenis kendaraan</option>
                                <option value="R4" <?= old('vehicle_type') === 'R4' ? 'selected' : '' ?>>R4</option>
                                <option value="R2" <?= old('vehicle_type') === 'R2' ? 'selected' : '' ?>>R2</option>
                            </select>
                            <label>Jenis kendaraan</label>
                        <?php endif; ?>
                    </div>
                    <div class="form-group floating-input">
                        <?php if (empty($years)): ?>
                            <div class="alert alert-warning mb-0">
                                Belum ada tahun di data box. Tambahkan data box dulu.
                            </div>
                        <?php else: ?>
                            <select name="year" id="year" class="custom-select" required>
                                <option value="" <?= old('year') === '' ? 'selected' : '' ?>>Tahun pembuatan</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?= esc((string) $year) ?>" <?= (string) $year === (string) old('year') ? 'selected' : '' ?>>
                                        <?= esc((string) $year) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label>Tahun pembuatan</label>
                        <?php endif; ?>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="plate_number" id="plate_number" class="form-control" value="<?= esc((string) old('plate_number')) ?>" placeholder=" " required>
                        <label>No. Polisi (contoh: DN 1234 AB)</label>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="no_bpkb" id="no_bpkb" class="form-control" value="<?= esc((string) old('no_bpkb')) ?>" placeholder=" ">
                        <label>No. BPKB (opsional)</label>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="no_rangka" id="no_rangka" class="form-control" value="<?= esc((string) old('no_rangka')) ?>" placeholder=" ">
                        <label>No. Rangka (opsional)</label>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="no_mesin" id="no_mesin" class="form-control" value="<?= esc((string) old('no_mesin')) ?>" placeholder=" ">
                        <label>No. Mesin (opsional)</label>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="merek" id="merek" class="form-control" value="<?= esc((string) old('merek')) ?>" placeholder=" ">
                        <label>Merek kendaraan (opsional)</label>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="tipe" id="tipe" class="form-control" value="<?= esc((string) old('tipe')) ?>" placeholder=" ">
                        <label>Tipe kendaraan (opsional)</label>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="isi_silinder" id="isi_silinder" class="form-control" value="<?= esc((string) old('isi_silinder')) ?>" placeholder=" ">
                        <label>Isi silinder (opsional)</label>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="warna" id="warna" class="form-control" value="<?= esc((string) old('warna')) ?>" placeholder=" ">
                        <label>Warna (opsional)</label>
                    </div>
                    <div class="form-group floating-input">
                        <input type="text" name="pengguna" id="pengguna" class="form-control" value="<?= esc((string) old('pengguna')) ?>" placeholder=" ">
                        <label>Pengguna kendaraan (opsional)</label>
                    </div>
                    <div class="form-group">
                        <input type="file" name="pdf" id="pdf" class="form-control-file" accept="application/pdf">
                        <small class="text-muted">Opsional, dapat ditambahkan nanti melalui edit data.</small>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" <?= empty($years) ? 'disabled' : '' ?>>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-import-bpkb" tabindex="-1" role="dialog" aria-labelledby="modalImportBpkbLabel" aria-hidden="true">
    <div class="modal-dialog modal-compact modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= site_url('admin/bpkb/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="vehicle_type_context" value="<?= esc((string) ($vehicleType ?? '')) ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportBpkbLabel">Import Data BPKB <?= esc((string) $vehicleLabel) ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Gunakan file `.xlsx`, `.xls`, atau `.csv`. Format lama maupun format baru tetap didukung, tetapi yang paling aman adalah file hasil `Download Excel` dari halaman ini.
                    </div>
                    <div class="mb-3">
                        <a href="<?= site_url('admin/bpkb/import-template' . ($vehicleType ? '?type=' . $vehicleType : '')) ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download"></i> Download Format Import
                        </a>
                    </div>
                    <div class="mb-3 small text-muted">
                        Kolom yang dibaca: `No. Polisi`, `No. BPKB`, `No. Rangka`, `No. Mesin`, `Merek`, `Tipe`, `Isi Silinder`, `Warna`, `Pengguna`, `Tahun`, `Jenis`.
                    </div>
                    <div class="form-group mb-0">
                        <label for="import_file">File Import</label>
                        <input type="file" name="import_file" id="import_file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $('#bpkb-table').DataTable({
            pageLength: 50,
            order: [[0, 'asc']],
            language: {
                search: '',
                emptyTable: 'Belum ada data BPKB.'
            }
        });
        <?php if ($shouldOpenModal): ?>
        $('#modal-bpkb').modal('show');
        <?php endif; ?>
        <?php if ($shouldOpenImportModal): ?>
        $('#modal-import-bpkb').modal('show');
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>
