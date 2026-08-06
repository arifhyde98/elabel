<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Data Surat Penyerahan | ArsipKu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $shouldOpenImportModal = session()->getFlashdata('openModal') === 'import';
?>
<style>
    #surat-penyerahan-table {
        width: 100% !important;
        table-layout: fixed;
    }

    .surat-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #surat-penyerahan-table th,
    #surat-penyerahan-table td {
        padding: 0.4rem 0.42rem;
        font-size: 0.76rem;
        line-height: 1.15;
        vertical-align: top;
        height: 48px;
        word-wrap: break-word;
        overflow-wrap: anywhere;
    }

    #surat-penyerahan-table th {
        text-align: left;
        vertical-align: middle;
    }

    #surat-penyerahan-table .btn-xs {
        padding: 0.16rem 0.3rem;
        font-size: 0.66rem;
    }

    .no-column {
        width: 38px;
        min-width: 38px;
        max-width: 38px;
        text-align: center;
        white-space: nowrap;
    }

    .surat-column {
        width: 145px;
        min-width: 145px;
        max-width: 145px;
    }

    .status-column {
        width: 150px;
        min-width: 150px;
        max-width: 150px;
    }

    .luas-column {
        width: 72px;
        min-width: 72px;
        max-width: 72px;
        text-align: right;
        white-space: nowrap;
    }

    .tahun-column {
        width: 62px;
        min-width: 62px;
        max-width: 62px;
        text-align: center;
        white-space: nowrap;
    }

    .lokasi-column {
        width: 130px;
        min-width: 130px;
        max-width: 130px;
    }

    .pemberi-column {
        width: 130px;
        min-width: 130px;
        max-width: 130px;
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

    .status-cell,
    .lokasi-cell,
    .pemberi-cell {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .page-actions {
        gap: 0.5rem;
    }

    .page-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    @media (max-width: 767.98px) {
        .surat-header {
            align-items: center !important;
            gap: 0.75rem;
        }

        .surat-header h1 {
            min-width: 0;
            margin-bottom: 0;
            font-size: 1.45rem;
            line-height: 1.15;
        }

        .page-actions {
            flex: 0 0 auto;
            gap: 0.45rem;
        }

        .page-action-btn {
            width: 48px;
            height: 48px;
            padding: 0;
            border-radius: 12px;
            font-size: 1rem;
            margin-right: 0 !important;
        }

        .page-action-btn i {
            margin: 0;
        }

        .page-action-label {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        #surat-penyerahan-table {
            width: 913px !important;
            min-width: 913px;
        }
    }

    @media (max-width: 420px) {
        .surat-header h1 {
            font-size: 1.28rem;
        }

        .page-action-btn {
            width: 44px;
            height: 44px;
            border-radius: 11px;
        }
    }
</style>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center surat-header">
        <h1>Data Surat Penyerahan</h1>
        <div class="d-flex align-items-center page-actions">
            <a href="<?= site_url('admin/surat-penyerahan/export') ?>" class="btn btn-success btn-sm mr-2 page-action-btn" aria-label="Download Excel" title="Download Excel">
                <i class="fas fa-file-excel"></i>
                <span class="page-action-label">Download Excel</span>
            </a>
            <button type="button" class="btn btn-secondary btn-sm mr-2 page-action-btn" data-toggle="modal" data-target="#modal-import-surat-penyerahan" aria-label="Import Excel" title="Import Excel">
                <i class="fas fa-file-upload"></i>
                <span class="page-action-label">Import Excel</span>
            </button>
            <a href="<?= site_url('admin/surat-penyerahan/create') ?>" class="btn btn-primary btn-sm page-action-btn" aria-label="Tambah Data" title="Tambah Data">
                <i class="fas fa-plus"></i>
                <span class="page-action-label">Tambah Data</span>
            </a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body table-responsive surat-table-wrap">
                <table id="surat-penyerahan-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="no-column">No</th>
                            <th>NIBAR</th>
                            <th class="surat-column">No. Surat</th>
                            <th class="status-column">Status Penggunaan</th>
                            <th class="luas-column">Luas</th>
                            <th class="tahun-column">Tahun</th>
                            <th class="lokasi-column">Lokasi</th>
                            <th class="pemberi-column">Pemberi Hibah</th>
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
                                <td data-search="<?= esc($nibarSearch) ?>"><?= esc($nibar !== '' ? $nibar : '-') ?></td>
                                <td class="surat-column"><?= esc((string) ($item['no_surat'] ?? '-')) ?></td>
                                <td class="status-column"><div class="status-cell"><?= esc((string) ($item['status_penggunaan'] ?? '-')) ?></div></td>
                                <td class="luas-column"><?= esc((string) ($item['luas'] ?? '-')) ?></td>
                                <td class="tahun-column"><?= esc((string) ($item['tahun'] ?? '-')) ?></td>
                                <td class="lokasi-column"><div class="lokasi-cell"><?= esc((string) ($item['lokasi'] ?? '-')) ?></div></td>
                                <td class="pemberi-column"><div class="pemberi-cell"><?= esc((string) ($item['pemberi_hibah'] ?? '-')) ?></div></td>
                                <td class="aksi-cell">
                                    <a href="<?= site_url('admin/surat-penyerahan/' . $item['id']) ?>" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= site_url('admin/surat-penyerahan/' . $item['id'] . '/edit') ?>" class="btn btn-warning btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= site_url('admin/surat-penyerahan/' . $item['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus data surat penyerahan ini?');">
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

<div class="modal fade" id="modal-import-surat-penyerahan" tabindex="-1" role="dialog" aria-labelledby="modalImportSuratPenyerahanLabel" aria-hidden="true">
    <div class="modal-dialog modal-compact modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= site_url('admin/surat-penyerahan/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportSuratPenyerahanLabel">Import Data Surat Penyerahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Gunakan file `.xlsx`, `.xls`, atau `.csv`. Format yang paling aman adalah file hasil `Download Format Import`.
                    </div>
                    <div class="mb-3">
                        <a href="<?= site_url('admin/surat-penyerahan/import-template') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download"></i> Download Format Import
                        </a>
                    </div>
                    <div class="mb-3 small text-muted">
                        Kolom yang dibaca: `NIBAR`, `No. Surat`, `Status Penggunaan`, `Luas`, `Tahun`, `Lokasi`, `Pemberi Hibah`.
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
        $('#surat-penyerahan-table').DataTable({
            pageLength: 50,
            lengthChange: false,
            order: [[0, 'asc']],
            autoWidth: false,
            columnDefs: [
                { targets: 1, visible: false, searchable: true }
            ],
            language: {
                search: '',
                emptyTable: 'Belum ada data surat penyerahan.'
            }
        });
        <?php if ($shouldOpenImportModal): ?>
        $('#modal-import-surat-penyerahan').modal('show');
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>
