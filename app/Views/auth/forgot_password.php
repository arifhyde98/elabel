<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | eLabel</title>
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
            <p class="login-box-msg">Masukkan email untuk reset password</p>

            <?= view('partials/alerts') ?>

            <?php if (session()->getFlashdata('dev_reset_link')): ?>
                <div class="alert alert-warning" role="alert">
                    Dev reset link:
                    <a href="<?= esc(session()->getFlashdata('dev_reset_link')) ?>">
                        <?= esc(session()->getFlashdata('dev_reset_link')) ?>
                    </a>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('forgot-password') ?>" method="post">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="<?= old('email') ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Kirim link reset</button>
            </form>

            <p class="mt-3 mb-0">
                <a href="<?= site_url('login') ?>">Kembali ke login</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
