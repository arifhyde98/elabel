<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | eLabel</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --dark-blue: #0f172a;
            --accent-gold: #f4c21f;
            --soft-blue: #3b82f6;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }
        /* Kolom Kiri: Branding */
        .branding-side {
            flex: 1.2;
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--primary-blue) 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
            overflow: hidden;
        }
        .branding-side::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.1;
        }
        .branding-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 500px;
        }
        .branding-content img {
            max-width: 120px;
            margin-bottom: 30px;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));
        }
        .branding-content h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .branding-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
            line-height: 1.2;
        }
        /* Kolom Kanan: Form */
        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #f8fafc;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            border-top: 5px solid var(--accent-gold);
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            font-weight: 700;
            color: var(--dark-blue);
            margin-bottom: 5px;
            font-size: 1.5rem;
        }
        .login-header p {
            color: #64748b;
            font-size: 0.9rem;
        }
        .form-group label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 8px;
            display: block;
        }
        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group-custom > i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .input-group-custom .form-control {
            border-radius: 0.75rem;
            height: 52px;
            padding-left: 45px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .input-group-custom .form-control[type="password"],
        .input-group-custom .form-control[name="password"] {
            padding-right: 48px;
        }
        .input-group-custom .form-control:focus {
            border-color: var(--soft-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-toggle-pass {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            z-index: 10;
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            padding: 0;
            border-radius: 50%;
            line-height: 1;
        }
        .btn-toggle-pass i {
            position: static;
            transform: none;
            color: inherit;
            font-size: 1rem;
        }
        .btn-toggle-pass:hover,
        .btn-toggle-pass:focus {
            color: var(--soft-blue);
            background: rgba(59, 130, 246, 0.08);
            outline: none;
        }
        .btn-primary {
            background-color: var(--primary-blue);
            border: none;
            border-radius: 0.75rem;
            height: 52px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--dark-blue);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .text-muted-gov {
            margin-top: 30px;
            color: #64748b;
            font-size: 0.875rem;
            text-align: center;
        }
        .forgot-link {
            display: block;
            text-align: right;
            font-size: 0.85rem;
            color: var(--soft-blue);
            margin-top: -10px;
            margin-bottom: 20px;
        }
        @media (max-width: 992px) {
            .branding-side {
                display: none;
            }
            .form-side {
                background: linear-gradient(135deg, var(--dark-blue) 0%, var(--primary-blue) 100%);
            }
            .login-card {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
        }
    </style>
</head>
<body class="hold-transition">
<?php
    $logoPath = FCPATH . 'Assets/logo.png';
    $logoSrc = base_url('public/Assets/logo.png');
    if (is_file($logoPath)) {
        $logoData = base64_encode((string) file_get_contents($logoPath));
        if ($logoData !== '') {
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }
    }
?>
<div class="login-wrapper">
    <!-- Bagian Branding (Kiri) -->
    <div class="branding-side">
        <div class="branding-content">
            <img src="<?= esc($logoSrc) ?>" alt="Logo Kabupaten Donggala">
            <h1>eLabel</h1>
            <p>Pemerintah Kabupaten Donggala<br>Badan Pengelolaan Keuangan dan Aset Daerah</p>
        </div>
    </div>

    <!-- Bagian Form (Kanan) -->
    <div class="form-side">
        <div class="login-card">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Masuk ke Sistem Informasi eLabel</p>
            </div>

            <?= view('partials/alerts') ?>

            <form action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Alamat Email</label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="nama@donggalakab.go.id" name="email" value="<?= old('email') ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" placeholder="Masukkan password" name="password" id="password" required>
                        <button type="button" class="btn-toggle-pass" id="togglePassword">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>
                <a href="<?= site_url('forgot-password') ?>" class="forgot-link">Lupa password?</a>
                <button type="submit" class="btn btn-primary">Masuk ke Dashboard</button>
            </form>

            <p class="text-muted-gov">
                &copy; <?= date('Y') ?> <b>Bidang Aset</b><br>
                BPKAD Kabupaten Donggala
            </p>
        </div>
    </div>
</div>
<script>
    (function () {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (!passwordInput || !toggleButton || !toggleIcon) {
            return;
        }

        toggleButton.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleIcon.classList.toggle('fa-eye', !isPassword);
            toggleIcon.classList.toggle('fa-eye-slash', isPassword);
            toggleButton.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
        });
    })();
</script>
</body>
</html>
