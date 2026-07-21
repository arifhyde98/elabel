<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Data Box <?= esc((string) $vehicleLabel) ?> | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .elabel-table-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 18px 30px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }
    .elabel-table {
        margin-bottom: 0;
    }
    .elabel-table thead th {
        background: linear-gradient(180deg, #f8fafc, #eff6ff);
        font-size: 0.66rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
    }
    .elabel-table tbody td {
        border-top: 1px solid #eef2f7;
        color: #0f172a;
        font-weight: 500;
        font-size: 0.72rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .elabel-table tbody tr:hover {
        background: #eff6ff;
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }
    .elabel-table tbody td:nth-child(2) {
        font-weight: 700;
        color: #1d4ed8;
    }
    .elabel-table tbody td:nth-child(5) {
        font-weight: 700;
        color: #0f172a;
    }
    .elabel-table .btn-xs {
        border-radius: 8px;
    }
    .elabel-table .btn-info {
        background: #e0f2fe;
        border-color: #bae6fd;
        color: #075985;
    }
    .elabel-table .btn-secondary {
        background: #e2e8f0;
        border-color: #e2e8f0;
        color: #0f172a;
    }
    .elabel-table .btn-danger {
        background: #fee2e2;
        border-color: #fecaca;
        color: #991b1b;
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
        border-radius: 8px;
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
        .elabel-table thead th {
            font-size: 0.62rem;
        }
        .vehicle-tab {
            min-width: 52px;
            padding: 0.3rem 0.36rem;
        }
    }
</style>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Data Box Arsip <?= esc((string) $vehicleLabel) ?></h1>
        <a href="<?= $vehicleRoute ? site_url('admin/boxes/' . $vehicleRoute . '/create') : site_url('admin/boxes/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Box
        </a>
    </div>
    <div class="container-fluid mt-2">
        <div class="vehicle-tabs" role="group" aria-label="Filter jenis kendaraan">
            <a href="<?= site_url('admin/boxes') ?>" class="vehicle-tab <?= empty($vehicleType) ? 'active' : '' ?>">
                <i class="fas fa-layer-group"></i>
                Semua
            </a>
            <a href="<?= site_url('admin/boxes/r4') ?>" class="vehicle-tab <?= ($vehicleType ?? '') === 'R4' ? 'active' : '' ?>">
                <i class="fas fa-car-side"></i>
                R4
            </a>
            <a href="<?= site_url('admin/boxes/r2') ?>" class="vehicle-tab <?= ($vehicleType ?? '') === 'R2' ? 'active' : '' ?>">
                <i class="fas fa-motorcycle"></i>
                R2
            </a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card elabel-table-card">
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle elabel-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Box</th>
                            <th>Lokasi</th>
                            <th>Tahun</th>
                            <th>Total BPKB</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($boxes)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data box.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($boxes as $box): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc((string) $box['box_code']) ?></td>
                                <td><?= esc((string) ($box['location'] ?? '-')) ?></td>
                                <td>
                                    <?php if (! empty($yearsMap[$box['id']])): ?>
                                        <?= esc(implode(', ', $yearsMap[$box['id']])) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= esc((string) ($box['bpkb_count'] ?? 0)) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/boxes/' . $box['id']) ?>" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i> <span class="btn-text">Detail</span>
                                    </a>
                                    <a href="<?= site_url('admin/boxes/' . $box['id'] . '/label?autoprint=1') ?>" class="btn btn-secondary btn-xs btn-print-label">
                                        <i class="fas fa-print"></i> <span class="btn-text">Cetak Label</span>
                                    </a>
                                    <form action="<?= site_url('admin/boxes/' . $box['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus box ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-xs">
                                            <i class="fas fa-trash"></i> <span class="btn-text">Hapus</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-print-label').forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                const url = btn.getAttribute('href');
                const frame = document.createElement('iframe');
                frame.style.position = 'fixed';
                frame.style.right = '0';
                frame.style.bottom = '0';
                frame.style.width = '0';
                frame.style.height = '0';
                frame.style.border = '0';
                frame.src = url;
                frame.onload = function () {
                    try {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                    } finally {
                        setTimeout(function () {
                            frame.remove();
                        }, 1000);
                    }
                };
                document.body.appendChild(frame);
            });
        });
    });
</script>
<?= $this->endSection() ?>
