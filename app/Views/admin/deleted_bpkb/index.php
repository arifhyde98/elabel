<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>BPKB Keluar | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Daftar BPKB Keluar</h1>
        <div class="d-flex">
            <a href="<?= site_url('admin/bpkb-deleted/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah BPKB Keluar
            </a>
            <a href="<?= site_url('admin/bpkb-deleted/export') ?>" class="btn btn-success btn-sm ml-2">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <?php
            $years = [];
            if (! empty($items)) {
                foreach ($items as $row) {
                    if (! empty($row['year'])) {
                        $years[] = (string) $row['year'];
                    }
                }
            }
            $years = array_values(array_unique($years));
            rsort($years);
        ?>
        <div class="mb-2">
            <div class="mb-2">
                <label for="filterYear" class="mr-2" style="font-size:0.78rem;">Filter Tahun:</label>
                <select id="filterYear" class="form-control form-control-sm d-inline-block" style="width: 120px; font-size:0.78rem;">
                    <option value="">Semua</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= esc((string) $year) ?>"><?= esc((string) $year) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <label for="filterType" class="mr-2" style="font-size:0.78rem;">Filter Jenis:</label>
                <select id="filterType" class="form-control form-control-sm d-inline-block" style="width: 120px; font-size:0.78rem;">
                    <option value="">Semua</option>
                    <option value="R4">R4</option>
                    <option value="R2">R2</option>
                </select>
            </div>
            <div class="mb-2">
                <label for="filterReason" class="mr-2" style="font-size:0.78rem;">Filter Alasan:</label>
                <select id="filterReason" class="form-control form-control-sm d-inline-block" style="width: 180px; font-size:0.78rem;">
                    <option value="">Semua</option>
                    <option value="Di pinjam">Di pinjam</option>
                    <option value="Penjualan">Penjualan</option>
                    <option value="Dihibahkan">Dihibahkan</option>
                    <option value="Kendaraan hilang">Kendaraan hilang</option>
                    <option value="Kendaraan tidak ditemukan">Kendaraan tidak ditemukan</option>
                </select>
            </div>
        </div>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Polisi</th>
                            <th>Jenis</th>
                            <th>Box</th>
                            <th>Tahun</th>
                            <th>Tanggal Hapus</th>
                            <th>Alasan</th>
                            <th>User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data BPKB keluar.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <tr data-year="<?= esc((string) ($item['year'] ?? '')) ?>" data-type="<?= esc((string) ($item['vehicle_type'] ?? '')) ?>" data-reason="<?= esc((string) ($item['reason'] ?? '')) ?>">
                            <td><?= $i++ ?></td>
                            <td><?= esc((string) $item['plate_number']) ?></td>
                            <td><?= esc((string) ($item['vehicle_type'] ?? '-')) ?></td>
                            <td><?= esc((string) $item['box_code']) ?></td>
                                <td><?= esc((string) ($item['year'] ?? '-')) ?></td>
                                <td><?= esc(date('Y-m-d', strtotime((string) $item['deleted_at']))) ?></td>
                                <td><?= esc((string) $item['reason']) ?></td>
                                <td><?= esc((string) $item['deleted_name']) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/bpkb-deleted/' . $item['id']) ?>" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i> <span class="btn-text">View</span>
                                    </a>
                                    <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#restoreModal<?= (int) $item['id'] ?>">
                                        <i class="fas fa-undo"></i> <span class="btn-text">Restore</span>
                                    </button>
                                    <div class="modal fade" id="restoreModal<?= (int) $item['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel<?= (int) $item['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <form action="<?= site_url('admin/bpkb-deleted/' . $item['id'] . '/restore') ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="restoreModalLabel<?= (int) $item['id'] ?>">Restore BPKB</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2">Data BPKB <?= esc((string) $item['plate_number']) ?> akan direstore.</p>
                                                        <div class="form-group mb-0">
                                                            <label>Password Login</label>
                                                            <input type="password" name="restore_password" class="form-control" autocomplete="current-password" required>
                                                            <small class="text-muted">Masukkan password akun yang sedang login untuk melanjutkan.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-undo"></i> Restore
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#permanentDeleteModal<?= (int) $item['id'] ?>">
                                        <i class="fas fa-trash"></i> <span class="btn-text">Hapus</span>
                                    </button>

                                    <div class="modal fade" id="permanentDeleteModal<?= (int) $item['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="permanentDeleteModalLabel<?= (int) $item['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <form action="<?= site_url('admin/bpkb-deleted/' . $item['id'] . '/delete') ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="permanentDeleteModalLabel<?= (int) $item['id'] ?>">Hapus Permanen BPKB Keluar</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2">Data BPKB <?= esc((string) $item['plate_number']) ?> akan dihapus permanen.</p>
                                                        <div class="form-group mb-0">
                                                            <label>Password Login</label>
                                                            <input type="password" name="delete_password" class="form-control" autocomplete="current-password" required>
                                                            <small class="text-muted">Masukkan password akun yang sedang login untuk melanjutkan.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i> Hapus Permanen
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
    (function () {
        if (window.jQuery) {
            $('.table .modal').appendTo('body');
        }

        const filterYear = document.getElementById('filterYear');
        const filterType = document.getElementById('filterType');
        const filterReason = document.getElementById('filterReason');
        const table = document.querySelector('.table');
        if (!filterYear || !filterType || !filterReason || !table) {
            return;
        }

        const rows = Array.from(table.querySelectorAll('tbody tr'));

        function applyFilter() {
            const yearValue = filterYear.value;
            const typeValue = filterType.value;
            const reasonValue = filterReason.value;
            rows.forEach((row) => {
                const rowYear = row.getAttribute('data-year') || '';
                const rowType = (row.getAttribute('data-type') || '').toUpperCase();
                const rowReason = row.getAttribute('data-reason') || '';
                const matchYear = !yearValue || rowYear === yearValue;
                const matchType = !typeValue || rowType === typeValue;
                const matchReason = !reasonValue || rowReason === reasonValue;
                row.style.display = matchYear && matchType && matchReason ? '' : 'none';
            });
        }

        filterYear.addEventListener('change', applyFilter);
        filterType.addEventListener('change', applyFilter);
        filterReason.addEventListener('change', applyFilter);
    })();
</script>
<?= $this->endSection() ?>
