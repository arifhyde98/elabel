<?= $this->extend('layouts/adminlte') ?>

<?php
    $pageTitle = $pageTitle ?? 'Edit User';
    $formAction = $formAction ?? site_url('admin/users/' . $user['id']);
    $backUrl = $backUrl ?? site_url('admin/users');
    $lockRole = (bool) ($lockRole ?? false);
    $profileMode = (bool) ($profileMode ?? false);
?>

<?= $this->section('title') ?><?= esc((string) $pageTitle) ?> | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><?= esc((string) $pageTitle) ?></h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= esc((string) $formAction) ?>" method="post" enctype="<?= $profileMode ? 'multipart/form-data' : 'application/x-www-form-urlencoded' ?>">
                    <?= csrf_field() ?>
                    <?php if ($profileMode): ?>
                        <?php
                            $profilePhoto = (string) ($user['profile_photo'] ?? '');
                            $profilePhotoSrc = $profilePhoto !== ''
                                ? site_url('admin/profile/photo') . '?v=' . urlencode((string) ($user['updated_at'] ?? time()))
                                : 'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/img/user2-160x160.jpg';
                        ?>
                        <div class="form-group">
                            <label>Foto Profil Saat Ini</label>
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?= esc($profilePhotoSrc) ?>" alt="Foto Profil" class="img-circle elevation-2 mr-3" style="width:72px;height:72px;object-fit:cover;">
                                <div class="text-muted small">Format JPG, PNG, atau WebP. Maksimal 2MB.</div>
                            </div>
                            <input type="file" name="profile_photo" id="profile_photo" class="form-control-file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        </div>
                    <?php endif; ?>
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
                        <?php if ($lockRole): ?>
                            <input type="text" id="role" class="form-control" value="<?= esc((string) ($user['role'] ?? '-')) ?>" disabled>
                        <?php else: ?>
                            <select name="role" id="role" class="form-control" required>
                                <option value="admin" <?= (old('role') ?: $user['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="super_admin" <?= (old('role') ?: $user['role']) === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                            </select>
                        <?php endif; ?>
                    </div>
                    <?php if ($profileMode): ?>
                        <hr id="ubah-password">
                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
                            <small class="text-muted">Isi hanya jika ingin mengganti password.</small>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                    <?php else: ?>
                    <div class="form-group">
                        <label for="password">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between">
                        <a href="<?= esc((string) $backUrl) ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary"><?= $profileMode ? 'Simpan Profil' : 'Simpan' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
