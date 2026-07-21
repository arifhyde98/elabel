<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Data Sertipikat Tanah | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $shouldOpenImportModal = session()->getFlashdata('openModal') === 'import';
?>
<style>
    #sertifikat-table {
        width: 100% !important;
        table-layout: fixed;
    }

    #sertifikat-table th,
    #sertifikat-table td {
        padding: 0.4rem 0.42rem;
        font-size: 0.76rem;
        line-height: 1.15;
        vertical-align: top;
        word-wrap: break-word;
    }

    #sertifikat-table .btn-xs {
        padding: 0.16rem 0.3rem;
        font-size: 0.66rem;
    }

    .aksi-column {
        width: 86px;
        min-width: 86px;
        white-space: nowrap;
    }

    .aksi-cell {
        white-space: nowrap;
    }

    .aksi-cell .btn,
    .aksi-cell form {
        display: inline-block;
        vertical-align: top;
        margin-bottom: 0.2rem;
    }

    .aksi-cell form {
        margin-right: 0;
    }

    .status-column {
        width: 150px;
        max-width: 150px;
    }

    .no-column {
        width: 38px;
        min-width: 38px;
        max-width: 38px;
        text-align: center;
        white-space: nowrap;
    }

    .sertifikat-column {
        width: 135px;
    }

    .nibar-column {
        width: 150px;
    }

    .luas-column {
        width: 72px;
        text-align: right;
        white-space: nowrap;
    }

    .lokasi-column {
        width: 96px;
    }

    .dinas-column {
        width: 92px;
    }

    .nibar-cell {
        min-width: 0;
        max-width: 100%;
        white-space: normal;
        word-break: break-all;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.15;
        font-size: 0.72rem;
    }

    .status-cell,
    .lokasi-cell,
    .dinas-cell {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
</style>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Data Sertipikat Tanah</h1>
        <div class="d-flex align-items-center">
            <a href="<?= site_url('admin/sertifikat/export') ?>" class="btn btn-success btn-sm mr-2">
                <i class="fas fa-file-excel"></i> Download Excel
            </a>
            <button type="button" class="btn btn-secondary btn-sm mr-2" data-toggle="modal" data-target="#modal-import-sertifikat">
                <i class="fas fa-file-upload"></i> Import Excel
            </button>
            <a href="<?= site_url('admin/sertifikat/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <table id="sertifikat-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="no-column">No</th>
                            <th class="sertifikat-column">No. Sertipikat</th>
                            <th class="nibar-column">NIBAR</th>
                            <th class="status-column">Status Penggunaan</th>
                            <th class="luas-column">Luas</th>
                            <th class="lokasi-column">Lokasi</th>
                            <th class="dinas-column">Dinas</th>
                            <th class="aksi-column">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <?php
                                $nibar = trim((string) ($item['nibar'] ?? ''));
                                $normalizedNibar = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($nibar)) ?? '';
                                $nibarSearch = trim($nibar . ' ' . $normalizedNibar);
                            ?>
                            <tr>
                                <td class="no-column"><?= $i++ ?></td>
                                <td class="sertifikat-column"><?= esc((string) $item['no_sertipikat']) ?></td>
                                <td class="nibar-column" data-search="<?= esc($nibarSearch) ?>"><div class="nibar-cell"><?= esc($nibar !== '' ? $nibar : '-') ?></div></td>
                                <td class="status-column"><div class="status-cell"><?= esc((string) ($item['status_penggunaan'] ?? '-')) ?></div></td>
                                <td class="luas-column"><?= esc((string) ($item['luas'] ?? '-')) ?></td>
                                <td class="lokasi-column"><div class="lokasi-cell"><?= esc((string) ($item['lokasi'] ?? '-')) ?></div></td>
                                <td class="dinas-column"><div class="dinas-cell"><?= esc((string) ($item['dinas'] ?? '-')) ?></div></td>
                                <td class="aksi-cell">
                                    <a href="<?= site_url('admin/sertifikat/' . $item['id']) ?>" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (! empty($item['pdf_path'])): ?>
                                        <a href="<?= site_url('admin/sertifikat/' . $item['id'] . '/view') ?>" target="_blank" class="btn btn-info btn-xs">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= site_url('admin/sertifikat/' . $item['id'] . '/edit') ?>" class="btn btn-warning btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= site_url('admin/sertifikat/' . $item['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus data sertipikat ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modal-import-sertifikat" tabindex="-1" role="dialog" aria-labelledby="modalImportSertifikatLabel" aria-hidden="true">
    <div class="modal-dialog modal-compact modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= site_url('admin/sertifikat/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportSertifikatLabel">Import Data Sertipikat Tanah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Gunakan file `.xlsx`, `.xls`, atau `.csv`. Format yang paling aman adalah file hasil `Download Format Import`.
                    </div>
                    <div class="mb-3">
                        <a href="<?= site_url('admin/sertifikat/import-template') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download"></i> Download Format Import
                        </a>
                    </div>
                    <div class="mb-3 small text-muted">
                        Kolom yang dibaca: `No. Sertipikat`, `NIBAR`, `Status Penggunaan`, `Spesifikasi`, `Luas`, `Tanggal Perolehan`, `Nilai Perolehan`, `Nama Pemilik`, `Cara Perolehan`, `Alamat`, `Lokasi`, `Dinas`.
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
        $('#sertifikat-table').DataTable({
            pageLength: 50,
            order: [[0, 'asc']],
            autoWidth: false,
            language: {
                search: '',
                emptyTable: 'Belum ada data sertipikat.'
            }
        });
        <?php if ($shouldOpenImportModal): ?>
        $('#modal-import-sertifikat').modal('show');
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>
