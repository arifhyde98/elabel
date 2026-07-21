<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | eLabel</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>eLabel</b> Reset
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <?php if (! empty($invalidToken)): ?>
                <div class="alert alert-danger">Token tidak valid atau sudah kedaluwarsa.</div>
                <a href="<?= site_url('forgot-password') ?>" class="btn btn-default btn-block">Minta token baru</a>
            <?php else: ?>
                <p class="login-box-msg">Masukkan password baru</p>
                <?= view('partials/alerts') ?>
                <form action="<?= site_url('reset-password') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= esc($token) ?>">
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password baru" name="password" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Konfirmasi password baru" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Update password</button>
                </form>
            <?php endif; ?>

            <p class="mt-3 mb-0">
                <a href="<?= site_url('login') ?>">Kembali ke login</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
