<?= $this->extend('layouts/userlte') ?>

<?= $this->section('title') ?>Peminjaman | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Riwayat Peminjaman</h1>
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
                            <th>Box</th>
                            <th>Tahun</th>
                            <th>Nomor Plat</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada peminjaman.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= esc((string) $item['box_code']) ?></td>
                                <td><?= esc((string) $item['bpkb_year']) ?></td>
                                <td><?= esc((string) $item['plate_number']) ?></td>
                                <td><?= esc((string) $item['requested_at']) ?></td>
                                <td><?= esc((string) $item['status']) ?></td>
                                <td><?= esc((string) ($item['note'] ?? '-')) ?></td>
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
