<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | eLabel</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <div class="register-logo">
        <b>eLabel</b> Register
    </div>

    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Daftar akun baru</p>

            <?= view('partials/alerts') ?>

            <form action="<?= site_url('register') ?>" method="post">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Nama lengkap" name="name" value="<?= old('name') ?>" required>
                </div>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="<?= old('email') ?>" required>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Konfirmasi password" name="password_confirmation" required>
                </div>
                <div class="row">
                    <div class="col-8">
                        <a href="<?= site_url('login') ?>" class="text-center">Sudah punya akun?</a>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
