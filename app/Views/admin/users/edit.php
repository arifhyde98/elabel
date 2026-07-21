<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Edit User | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Edit User</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/users/' . $user['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc((string) old('name') ?: $user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= esc((string) old('email') ?: $user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="admin" <?= (old('role') ?: $user['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="super_admin" <?= (old('role') ?: $user['role']) === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
