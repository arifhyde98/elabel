<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Data Box Sertipikat | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Data Box Sertipikat Tanah</h1>
        <a href="<?= site_url('admin/sertifikat-boxes/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Box
        </a>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Box</th>
                            <th>Lokasi</th>
                            <th>Total Sertipikat</th>
                            <th>Kapasitas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($boxes)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data box sertipikat.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($boxes as $box): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc((string) $box['box_code']) ?></td>
                                <td><?= esc((string) ($box['lokasi'] ?? '-')) ?></td>
                                <td><?= esc((string) ($box['sertifikat_count'] ?? 0)) ?></td>
                                <td>Maks <?= esc((string) $maxPerBox) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/sertifikat-boxes/' . $box['id']) ?>" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i> <span class="btn-text">Detail</span>
                                    </a>
                                    <a href="<?= site_url('admin/sertifikat-boxes/' . $box['id'] . '/label?autoprint=1') ?>" class="btn btn-secondary btn-xs btn-print-label">
                                        <i class="fas fa-print"></i> <span class="btn-text">Cetak Label</span>
                                    </a>
                                    <form action="<?= site_url('admin/sertifikat-boxes/' . $box['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus box sertipikat ini?');">
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
