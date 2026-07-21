<?= $this->extend('layouts/userlte') ?>

<?= $this->section('title') ?>Daftar BPKB | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Daftar BPKB</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table id="bpkb-table-user" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Box</th>
                            <th>Tahun Pembuatan</th>
                            <th>Nomor Plat</th>
                            <th>Status</th>
                            <th>File</th>
                            <th>Peminjaman</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data BPKB.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= esc((string) $item['box_code']) ?></td>
                                <td><?= esc((string) $item['year']) ?></td>
                                <td><?= esc((string) $item['plate_number']) ?></td>
                                <td><?= esc((string) $item['status']) ?></td>
                                <td>
                                    <?php if (! empty($item['pdf_path'])): ?>
                                        <a href="<?= site_url('bpkb/' . $item['id'] . '/download') ?>" class="btn btn-sm btn-info">Download</a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['status'] === 'Tersedia'): ?>
                                        <form action="<?= site_url('loans') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="bpkb_id" value="<?= esc((string) $item['id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-primary">Ajukan</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak tersedia</span>
                                    <?php endif; ?>
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
    $(function () {
        $('#bpkb-table-user').DataTable({
            pageLength: 10,
            order: [[1, 'desc']]
        });
    });
</script>
<?= $this->endSection() ?>
