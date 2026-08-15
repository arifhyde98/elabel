<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Tanah E-BMD | Master Data | ArsipKu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    #ebmd-tanah-table {
        width: 100% !important;
    }
    #ebmd-tanah-table th,
    #ebmd-tanah-table td {
        padding: 0.5rem 0.6rem;
        font-size: 0.78rem;
        vertical-align: middle;
    }
    .badge-archive {
        font-size: 0.72rem;
        padding: 4px 10px;
        border-radius: 50rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-archive-success {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-archive-warning {
        background-color: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }
    .badge-archive-danger {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
</style>

<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-map-marked-alt text-primary mr-2"></i>Master Data Tanah E-BMD
            </h1>
            <small class="text-muted">Sinkronisasi Real-Time Master Data Aset Tanah SIPAT dengan Arsip Fisik eLabel</small>
        </div>
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
            <li class="breadcrumb-item">Master Data</li>
            <li class="breadcrumb-item active">Tanah E-BMD</li>
        </ol>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>

        <?php if (!$apiConnected): ?>
            <div class="alert alert-warning border-0 shadow-sm rounded-lg d-flex align-items-center justify-content-between p-3 mb-3">
                <div>
                    <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                    <strong>Integrasi API SIPAT Terkendala:</strong> <?= esc($apiError) ?>
                </div>
                <a href="<?= site_url('admin/ebmd-tanah') ?>" class="btn btn-outline-dark btn-sm rounded-pill">
                    <i class="fas fa-sync-alt mr-1"></i> Coba Lagi
                </a>
            </div>
        <?php else: ?>
            <div class="alert alert-success border-0 shadow-sm rounded-lg d-flex align-items-center justify-content-between p-3 mb-3">
                <div class="d-flex align-items-center">
                    <span class="spinner-grow spinner-grow-sm text-success mr-2" role="status"></span>
                    <div>
                        <strong>Terhubung Real-Time ke SIPAT API:</strong>
                        <span class="ml-1 text-muted">Menampilkan <?= count($sipatAssets) ?> data aset tanah terkini dari sistem SIPAT.</span>
                    </div>
                </div>
                <button type="button" onclick="window.location.reload();" class="btn btn-success btn-sm rounded-pill">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh Data
                </button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h3 class="card-title text-dark font-weight-bold mb-0">
                    <i class="fas fa-database mr-2 text-primary"></i> Daftar Master Aset Tanah (SIPAT)
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table id="ebmd-tanah-table" class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th>Nama Aset Tanah</th>
                                <th style="width: 150px;">OPD Pengelola</th>
                                <th style="width: 90px;" class="text-right">Luas (m²)</th>
                                <th style="width: 140px;">Status SIPAT</th>
                                <th style="width: 180px;">Status Arsip eLabel</th>
                                <th style="width: 100px;" class="text-center">Aksi Arsip</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($sipatAssets as $row): ?>
                                <?php
                                    $nibar = trim((string) ($row['kode_aset'] ?? ''));
                                    $cleanNibar = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($nibar));
                                    $localCert = $archiveMap[$cleanNibar] ?? null;
                                    $queryData = [
                                        'nibar'             => $nibar,
                                        'nama'              => $row['nama_aset'] ?? '',
                                        'opd'               => $row['opd'] ?? '',
                                        'luas'              => $row['luas'] ?? '',
                                        'tanggal_perolehan' => $row['tanggal_perolehan'] ?? '',
                                        'nilai_perolehan'   => $row['harga_perolehan'] ?? '',
                                        'cara_perolehan'    => $row['dasar_perolehan'] ?? '',
                                        'alamat'            => $row['alamat'] ?? '',
                                        'peruntukan'        => $row['peruntukan'] ?? ''
                                    ];
                                ?>
                                <tr>
                                    <td class="text-center text-muted font-weight-bold"><?= $i++ ?></td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= esc($row['nama_aset']) ?></div>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?= esc($row['peruntukan'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <span class="text-secondary small"><?= esc($row['opd'] ?? '-') ?></span>
                                    </td>
                                    <td class="text-right font-monospace">
                                        <?= !empty($row['luas']) ? esc(number_format((float)$row['luas'], 2, '.', ',')) : '-' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['status_terkini'])): ?>
                                            <span class="badge badge-info px-2 py-1">
                                                <?= esc($row['status_terkini']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1">Belum Terproses</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($localCert): ?>
                                            <?php if (!empty($localCert['box_code'])): ?>
                                                <span class="badge-archive badge-archive-success">
                                                    <i class="fas fa-box-open mr-1"></i> Box: <?= esc($localCert['box_code']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge-archive badge-archive-warning">
                                                    <i class="fas fa-file-alt mr-1"></i> Tersedia (Tanpa Box)
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge-archive badge-archive-danger">
                                                <i class="fas fa-times-circle mr-1"></i> Belum Didaftarkan
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($localCert): ?>
                                            <a href="<?= site_url('admin/sertifikat/' . $localCert['id']) ?>" class="btn btn-info btn-xs shadow-sm" title="Lihat Detail Sertifikat eLabel">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= site_url('admin/sertifikat/create?' . http_build_query($queryData)) ?>" class="btn btn-primary btn-xs shadow-sm" title="Daftarkan ke Sertifikat eLabel">
                                                <i class="fas fa-plus-circle mr-1"></i> Arsipkan
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $('#ebmd-tanah-table').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            order: [[0, 'asc']],
            autoWidth: false,
            language: {
                search: "Cari Data:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ aset tanah",
                emptyTable: "Belum ada data aset tanah dari SIPAT."
            }
        });
    });
</script>
<?= $this->endSection() ?>
