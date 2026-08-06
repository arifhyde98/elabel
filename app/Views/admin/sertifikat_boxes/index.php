<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Data Box Sertipikat | ArsipKu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .box-create-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    .box-actions {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .box-actions .btn,
    .box-actions form {
        flex: 0 0 auto;
        margin: 0;
    }

    .box-actions form {
        display: inline-flex;
    }

    @media (max-width: 767.98px) {
        .box-header {
            gap: 0.75rem;
        }

        .box-header h1 {
            min-width: 0;
            margin-bottom: 0;
            font-size: 1.45rem;
            line-height: 1.15;
        }

        .box-create-btn {
            flex: 0 0 auto;
            width: 48px;
            height: 48px;
            padding: 0;
            border-radius: 12px;
            font-size: 1rem;
        }

        .box-create-btn i {
            margin: 0;
        }

        .box-create-label {
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
    }
</style>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center box-header">
        <h1>Data Box Sertipikat Tanah</h1>
        <a href="<?= site_url('admin/sertifikat-boxes/create') ?>" class="btn btn-primary btn-sm box-create-btn" aria-label="Tambah Box" title="Tambah Box">
            <i class="fas fa-plus"></i>
            <span class="box-create-label">Tambah Box</span>
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
                                <td class="box-actions">
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
