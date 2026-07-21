<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Users | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Manajemen User</h1>
        <?php if (session()->get('user_role') === 'super_admin'): ?>
            <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah User
            </a>
        <?php endif; ?>
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
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada user.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($items as $user): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc((string) $user['name']) ?></td>
                                <td><?= esc((string) $user['email']) ?></td>
                                <td><?= esc((string) str_replace('_', ' ', $user['role'])) ?></td>
                                <td><?= (int) $user['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?></td>
                                <td>
                                    <?php if (session()->get('user_role') === 'super_admin'): ?>
                                        <a href="<?= site_url('admin/users/' . $user['id'] . '/edit') ?>" class="btn btn-info btn-xs">Edit</a>
                                        <form action="<?= site_url('admin/users/' . $user['id'] . '/toggle') ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-warning btn-xs">Toggle</button>
                                        </form>
                                        <form action="<?= site_url('admin/users/' . $user['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Akses dibatasi</span>
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
