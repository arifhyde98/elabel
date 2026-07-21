<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Tambah Penghapusan BPKB | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Tambah Penghapusan BPKB</h1>
        <a href="<?= site_url('admin/bpkb-deleted') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table id="bpkb-delete-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Box</th>
                            <th>Tahun</th>
                            <th>Nomor Plat</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>PDF</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data BPKB.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc((string) $item['box_code']) ?></td>
                                <td><?= esc((string) $item['year']) ?></td>
                                <td><?= esc((string) $item['plate_number']) ?></td>
                                <td><?= esc((string) ($item['vehicle_type'] ?? '-')) ?></td>
                                <td><?= esc((string) $item['status']) ?></td>
                                <td>
                                    <?php if (! empty($item['pdf_path'])): ?>
                                        <span class="badge badge-success">Ada</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('admin/bpkb/' . $item['id']) ?>" class="btn btn-info btn-xs mr-1">
                                        <i class="fas fa-eye"></i> <span class="btn-text">View</span>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal<?= (int) $item['id'] ?>">
                                        <i class="fas fa-trash"></i> <span class="btn-text">Hapus</span>
                                    </button>

                                    <div class="modal fade" id="deleteModal<?= (int) $item['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?= (int) $item['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?= (int) $item['id'] ?>">Alasan Penghapusan</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="<?= site_url('admin/bpkb/' . $item['id'] . '/delete') ?>" method="post" enctype="multipart/form-data">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Alasan Penghapusan</label>
                                                            <select name="reason" class="form-control" required>
                                                                <option value="">Pilih alasan</option>
                                                                <option value="Di pinjam">Di pinjam</option>
                                                                <option value="Penjualan">Penjualan</option>
                                                                <option value="Dihibahkan">Dihibahkan</option>
                                                                <option value="Kendaraan hilang">Kendaraan hilang</option>
                                                                <option value="Kendaraan tidak ditemukan">Kendaraan tidak ditemukan</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Upload Dokumen Pendukung</label>
                                                            <input type="file" name="support_doc" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                                                            <small class="text-muted">Format: PDF/JPG/PNG, maks 5MB.</small>
                                                        </div>
                                                        <div class="form-group mb-0">
                                                            <label>Keterangan</label>
                                                            <textarea name="reason_detail" class="form-control" rows="3" placeholder="Tambahkan keterangan bila perlu"></textarea>
                                                        </div>
                                                        <div class="form-group mt-3 mb-0">
                                                            <label>Password Login</label>
                                                            <input type="password" name="delete_password" class="form-control" autocomplete="current-password" required>
                                                            <small class="text-muted">Masukkan password akun yang sedang login untuk melanjutkan.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
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
    $(function () {
        $('#bpkb-delete-table .modal').appendTo('body');

        $('#bpkb-delete-table').DataTable({
            pageLength: 10,
            order: [[2, 'desc']]
        });
    });
</script>
<?= $this->endSection() ?>
