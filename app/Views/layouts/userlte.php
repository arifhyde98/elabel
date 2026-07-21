<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($this->renderSection('title') ?: 'User Dashboard | eLabel') ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.8/css/dataTables.bootstrap4.min.css">
    <style>
        :root {
            --elabel-primary: #0f766e;
            --elabel-dark: #0b3b36;
            --elabel-surface: #f5f7fb;
        }
        body {
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", sans-serif;
            background: var(--elabel-surface);
        }
        .main-header.navbar {
            background: #ffffff;
            border-bottom: 1px solid rgba(15, 118, 110, 0.15);
        }
        .main-sidebar {
            background: linear-gradient(180deg, #0f766e 0%, #0b3b36 100%);
        }
        .brand-link {
            background: rgba(255, 255, 255, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .brand-text {
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .nav-sidebar .nav-link.active {
            background: rgba(0, 0, 0, 0.35);
            color: #fff;
            border-left: 4px solid #f4c21f;
            padding-left: 8px;
        }
        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            border-radius: 6px;
            margin: 4px 10px;
            padding: 10px 12px;
            font-weight: 600;
        }
        .nav-sidebar .nav-link:hover {
            background: rgba(0, 0, 0, 0.2);
            color: #fff;
        }
        .content-wrapper {
            background: var(--elabel-surface);
        }
        .card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }
        .btn-primary {
            background: var(--elabel-primary);
            border-color: var(--elabel-primary);
        }
        .btn-primary:hover {
            background: #0d6b63;
            border-color: #0d6b63;
        }
        .table thead th {
            background: #eef4f3;
            border-bottom: 0;
        }
        .table {
            font-size: 0.85rem;
        }
        @media (max-width: 991.98px) {
            .table {
                font-size: 0.8rem;
            }
            .nav-sidebar .nav-link {
                font-size: 0.88rem;
            }
        }
        @media (max-width: 767.98px) {
            .table {
                font-size: 0.72rem;
            }
            .btn {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }
            .content-header h1 {
                font-size: 1.1rem;
            }
            .nav-sidebar .nav-link {
                font-size: 0.82rem;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= site_url('dashboard') ?>" class="nav-link">Home</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <form action="<?= site_url('logout') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm mt-1">Logout</button>
                </form>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?= site_url('dashboard') ?>" class="brand-link">
            <i class="fas fa-tags ml-2 mr-2"></i>
            <span class="brand-text font-weight-light">eLabel User</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?= esc((string) session()->get('user_name')) ?></a>
                </div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="<?= site_url('dashboard') ?>" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('bpkb') ?>" class="nav-link <?= ($activeMenu ?? '') === 'bpkb' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-folder-open"></i>
                            <p>Daftar BPKB</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('loans') ?>" class="nav-link <?= ($activeMenu ?? '') === 'loans' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-signature"></i>
                            <p>Peminjaman</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="main-footer">
        <strong>eLabel User Panel</strong>
    </footer>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.8/js/dataTables.bootstrap4.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
