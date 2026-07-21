<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($this->renderSection('title') ?: 'Admin Dashboard | eLabel') ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.8/css/dataTables.bootstrap4.min.css">
    <style>
        :root {
            --ta-primary: #1d4ed8;
            --ta-primary-dark: #123a7a;
            --ta-primary-soft: #dbeafe;
            --ta-slate-900: #0b1220;
            --ta-navy: #07152f;
            --ta-navy-2: #0e2d63;
            --ta-accent-gold: #d8a928;
            --ta-slate-700: #334155;
            --ta-slate-600: #475569;
            --ta-slate-500: #64748b;
            --ta-slate-300: #cbd5e1;
            --ta-slate-200: #e2e8f0;
            --ta-slate-100: #f1f5f9;
            --ta-surface: #f8fafc;
            --ta-success: #22c55e;
            --ta-warning: #f59e0b;
            --ta-info: #38bdf8;
            --topbar-height: 56px;
        }
        body {
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", sans-serif;
            background:
                linear-gradient(135deg, rgba(219, 234, 254, 0.88), rgba(248, 250, 252, 0.96) 42%, #ffffff 100%),
                repeating-linear-gradient(135deg, rgba(29, 78, 216, 0.04) 0 1px, transparent 1px 22px);
            color: var(--ta-slate-900);
            font-size: 0.88rem;
            line-height: 1.45;
        }
        .main-header.navbar {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--ta-slate-200);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1030;
            height: var(--topbar-height);
        }
        .main-header.navbar .nav-link {
            padding-top: 0.45rem;
            padding-bottom: 0.45rem;
        }
        .main-header.navbar .nav-link,
        .main-header.navbar .navbar-nav .nav-link,
        .main-header.navbar .navbar-nav .nav-link span {
            color: var(--ta-slate-700);
            font-weight: 600;
        }
        .topbar-identity {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-width: 0;
            padding-left: 0.3rem;
        }
        .topbar-identity img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            filter: drop-shadow(0 5px 8px rgba(15, 23, 42, 0.14));
        }
        .topbar-identity strong {
            display: block;
            color: var(--ta-slate-900);
            font-size: 0.86rem;
            line-height: 1.05;
        }
        .topbar-identity > span > span {
            display: block;
            color: var(--ta-slate-500);
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            line-height: 1.1;
            text-transform: uppercase;
        }
        .main-header.navbar .nav-link:hover {
            color: var(--ta-primary);
        }
        .burger {
            position: relative;
            width: 22px;
            height: 16px;
            background: transparent;
            cursor: pointer;
            display: inline-block;
        }
        .burger input {
            display: none;
        }
        .burger span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: var(--ta-slate-700);
            border-radius: 999px;
            opacity: 1;
            left: 0;
            transform: rotate(0deg);
            transition: 0.25s ease-in-out;
        }
        .burger span:nth-of-type(1) {
            top: 0;
            transform-origin: left center;
        }
        .burger span:nth-of-type(2) {
            top: 50%;
            transform: translateY(-50%);
            transform-origin: left center;
        }
        .burger span:nth-of-type(3) {
            top: 100%;
            transform-origin: left center;
            transform: translateY(-100%);
        }
        .burger input:checked ~ span:nth-of-type(1) {
            transform: rotate(45deg);
            top: 0;
            left: 2px;
        }
        .burger input:checked ~ span:nth-of-type(2) {
            width: 0;
            opacity: 0;
        }
        .burger input:checked ~ span:nth-of-type(3) {
            transform: rotate(-45deg);
            top: 14px;
            left: 2px;
        }
        .main-sidebar {
            background:
                linear-gradient(180deg, rgba(7, 21, 47, 0.98) 0%, rgba(14, 45, 99, 0.98) 58%, rgba(8, 24, 55, 0.98) 100%),
                repeating-linear-gradient(45deg, rgba(255, 255, 255, 0.055) 0 1px, transparent 1px 18px);
            top: 0;
            height: 100vh;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 18px 0 36px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }
        .main-sidebar .sidebar {
            height: calc(100vh - 66px);
            overflow: hidden !important;
            padding: 0.75rem 0 0;
        }
        .main-sidebar,
        .main-sidebar *,
        .nav-sidebar,
        .nav-sidebar .nav-item,
        .nav-sidebar .nav-link {
            max-width: 100%;
            box-sizing: border-box;
        }
        .brand-link {
            background: rgba(255, 255, 255, 0.06);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            min-height: 66px;
            padding: 0.72rem 0.85rem;
            overflow: hidden;
        }
        .brand-text {
            font-weight: 700;
            letter-spacing: 0;
            color: #fff !important;
            white-space: normal;
            line-height: 1.15;
        }
        .brand-text small {
            color: #bfdbfe;
            font-size: 0.68rem;
            font-weight: 600;
        }
        .brand-link img {
            background: #fff;
            border: 2px solid rgba(216, 169, 40, 0.7);
            filter: drop-shadow(0 8px 14px rgba(0,0,0,0.22));
            height: 42px;
            max-height: 42px;
            object-fit: contain;
            padding: 3px;
        }
        .sidebar-collapse .main-sidebar,
        .sidebar-mini.sidebar-collapse .main-sidebar:hover {
            overflow: hidden;
        }
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .brand-link {
            justify-content: center;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .brand-image {
            margin-left: 0;
            margin-right: 0;
        }
        .brand-link .nav-icon,
        .brand-link i.fas {
            font-size: 0.8rem;
            color: #c7d2fe;
        }
        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            border-radius: 8px;
            margin: 5px 10px;
            padding: 9px 11px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
            width: auto;
        }
        .nav-sidebar .nav-link .nav-icon {
            font-size: 0.9rem;
        }
        .nav-sidebar .nav-link:hover {
            background: rgba(96, 165, 250, 0.16);
            color: #fff;
            transform: translateX(2px);
        }
        .nav-sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(216, 169, 40, 0.2), rgba(59, 130, 246, 0.16));
            color: #fff;
            border-left: 0;
            padding-left: 12px;
            box-shadow: inset 4px 0 0 var(--ta-accent-gold);
        }
        .nav-sidebar .nav-treeview {
            padding-left: 0;
            overflow: hidden;
        }
        .nav-sidebar .nav-treeview .nav-link {
            background: rgba(15, 23, 42, 0.35);
            color: #e2e8f0;
            margin: 0 10px 5px 10px;
            font-weight: 500;
            font-size: 0.84rem;
            border-radius: 8px;
            padding-left: 38px;
        }
        .nav-sidebar .nav-treeview .nav-icon {
            display: none;
        }
        .nav-sidebar .nav-treeview .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 0;
            padding-left: 38px;
            box-shadow: inset 4px 0 0 var(--ta-accent-gold);
        }
        .content-wrapper {
            background: transparent;
            margin-top: var(--topbar-height);
            position: relative;
        }
        .content-wrapper::before {
            content: "";
            position: fixed;
            inset: var(--topbar-height) 0 auto 0;
            height: 220px;
            pointer-events: none;
            background:
                linear-gradient(135deg, rgba(29, 78, 216, 0.1), rgba(216, 169, 40, 0.07) 46%, transparent 72%),
                repeating-linear-gradient(135deg, rgba(15, 23, 42, 0.035) 0 1px, transparent 1px 18px);
            z-index: 0;
        }
        .content-header,
        .content-wrapper > .content {
            position: relative;
            z-index: 1;
        }
        .content-header h1 {
            font-weight: 700;
            color: var(--ta-slate-900);
            font-size: 1.2rem;
        }
        .content-header .container-fluid {
            padding-left: 0.8rem;
            padding-right: 0.8rem;
        }
        .content-wrapper > .content {
            padding-left: 0.8rem;
            padding-right: 0.8rem;
        }
        .content-wrapper .container-fluid {
            max-width: 100%;
        }
        .table-responsive {
            width: 100%;
        }
        .breadcrumb {
            background: transparent;
            margin-bottom: 0;
        }
        .breadcrumb-item a {
            color: var(--ta-slate-500);
            font-weight: 600;
        }
        .breadcrumb-item.active {
            color: var(--ta-slate-700);
            font-weight: 700;
        }
        .card {
            border: 1px solid var(--ta-slate-200);
            border-radius: 8px;
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
            border-top: 3px solid var(--ta-accent-gold);
            overflow: hidden;
        }
        .card-body {
            padding: 1rem;
        }
        .card-header {
            padding: 0.8rem 1rem;
        }
        .card-header {
            background: linear-gradient(90deg, #ffffff, #f8fafc);
            border-bottom: 1px solid var(--ta-slate-200);
            font-weight: 700;
        }
        .card-title {
            font-weight: 700;
        }
        .btn {
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            padding: 0.35rem 0.7rem;
            font-size: 0.78rem;
        }
        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
        }
        .btn-xs {
            padding: 0.22rem 0.45rem;
            font-size: 0.68rem;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.14);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--ta-primary), #2563eb);
            border-color: #1d4ed8;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--ta-primary-dark), #1d4ed8);
            border-color: var(--ta-primary-dark);
        }
        .btn-info {
            background: #e0f2fe;
            border-color: #bae6fd;
            color: #075985;
        }
        .btn-info:hover {
            background: #bae6fd;
            border-color: #7dd3fc;
            color: #075985;
        }
        .btn-success {
            background: #dcfce7;
            border-color: #bbf7d0;
            color: #166534;
        }
        .btn-success:hover {
            background: #bbf7d0;
            border-color: #86efac;
            color: #14532d;
        }
        .btn-warning {
            background: #fef3c7;
            border-color: #fde68a;
            color: #92400e;
        }
        .btn-warning:hover {
            background: #fde68a;
            border-color: #facc15;
            color: #78350f;
        }
        .btn-danger {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .btn-danger:hover {
            background: #fecaca;
            border-color: #fca5a5;
            color: #7f1d1d;
        }
        .btn-outline-secondary {
            color: var(--ta-slate-600);
            border-color: var(--ta-slate-200);
        }
        .btn-outline-secondary:hover {
            color: var(--ta-slate-900);
            border-color: var(--ta-slate-300);
            background: #fff;
        }
        .table {
            font-size: 0.78rem;
        }
        .table thead th {
            background: linear-gradient(180deg, #f8fafc, #eff6ff);
            border-bottom: 1px solid var(--ta-slate-200);
            color: var(--ta-slate-600);
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
        }
        .table td {
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }
        .table td {
            border-top: 1px solid var(--ta-slate-200);
        }
        .table tbody tr:hover {
            background: #eff6ff;
        }
        .table-bordered,
        .table-bordered td,
        .table-bordered th {
            border-color: #e2e8f0;
        }
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #dbe4f0;
            border-radius: 8px;
            color: var(--ta-slate-700);
        }
        .page-item.active .page-link {
            background: var(--ta-primary);
            border-color: var(--ta-primary);
        }
        .page-link {
            color: var(--ta-primary);
            border-color: #e2e8f0;
        }
        .small-box {
            border-radius: 8px;
            border: 1px solid var(--ta-slate-200);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
            background: #fff;
            color: var(--ta-slate-900);
        }
        .small-box .inner h3 {
            font-size: 1.2rem;
        }
        .small-box .inner p {
            font-size: 0.82rem;
        }
        .small-box.bg-success {
            background: linear-gradient(140deg, #ecfdf3, #e0f7ef);
            border-color: #bbf7d0;
        }
        .small-box.bg-warning {
            background: linear-gradient(140deg, #fff7ed, #ffedd5);
            border-color: #fed7aa;
        }
        .small-box.bg-info {
            background: linear-gradient(140deg, #e0f2fe, #dbeafe);
            border-color: #bae6fd;
        }
        .small-box .icon {
            color: rgba(15, 23, 42, 0.25);
        }
        .dropdown-menu {
            border-radius: 8px;
            border: 1px solid var(--ta-slate-200);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.15);
        }
        .modal-title {
            font-size: 1rem;
        }
        .modal-body {
            font-size: 0.84rem;
        }
        .modal-content {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }
        .modal-dialog.modal-compact {
            max-width: 520px;
        }
        .modal-header {
            background: linear-gradient(120deg, var(--ta-navy-2), #1d4ed8);
            color: #fff;
            border-bottom: 0;
            padding: 0.9rem 1.1rem;
        }
        .modal-header .close {
            color: #fff;
            opacity: 0.8;
            text-shadow: none;
        }
        .modal-header .close:hover {
            opacity: 1;
        }
        .modal-body {
            background: #f8fafc;
            padding: 1rem 1.1rem;
        }
        .modal-footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 0.75rem 1.1rem;
        }
        .modal .form-group label {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }
        .modal .form-control,
        .modal .custom-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .modal .form-control:focus,
        .modal .custom-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        }
        .modal .alert-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            border-radius: 12px;
        }
        .modal .floating-input {
            position: relative;
            margin-bottom: 1rem;
        }
        .modal .floating-input .form-control,
        .modal .floating-input .custom-select {
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            background: transparent;
            padding: 0.7rem 0.8rem;
            height: auto;
        }
        .modal .floating-input .custom-select:invalid {
            color: #94a3b8;
        }
        .modal .floating-input .custom-select:valid {
            color: #0f172a;
        }
        .modal .floating-input label {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-weight: 600;
            pointer-events: none;
            transition: 0.2s ease;
            background: transparent;
            padding: 0 0.2rem;
        }
        .modal .floating-input .form-control:focus,
        .modal .floating-input .custom-select:focus,
        .modal .floating-input .form-control:not(:placeholder-shown),
        .modal .floating-input .custom-select:not([value=""]) {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.12);
        }
        .modal .floating-input .form-control:focus + label,
        .modal .floating-input .custom-select:focus + label,
        .modal .floating-input .form-control:not(:placeholder-shown) + label,
        .modal .floating-input .custom-select:not([value=""]) + label {
            top: -0.45rem;
            transform: translateY(0);
            font-size: 0.7rem;
            color: #3b82f6;
            background: #f8fafc;
        }
        .modal.fade .modal-dialog {
            transform: translateY(12px) scale(0.98);
            transition: transform 0.25s ease, opacity 0.25s ease;
        }
        .modal.show .modal-dialog {
            transform: translateY(0) scale(1);
        }
        .form-control,
        .custom-select {
            font-size: 0.82rem;
            padding: 0.4rem 0.6rem;
        }
        .form-control-file {
            font-size: 0.8rem;
        }
        .footer-version {
            font-size: 0.75rem;
        }
        .footer-version b {
            font-size: 0.72rem;
        }
        @media (max-width: 991.98px) {
            .main-sidebar {
                top: 0;
                height: 100vh;
            }
            .main-sidebar .sidebar {
                height: calc(100vh - 66px);
            }
            .main-header .navbar-nav .nav-link span {
                font-size: 0.85rem;
            }
            .card {
                border-radius: 12px;
            }
            .table {
                font-size: 0.8rem;
            }
            .nav-sidebar .nav-link {
                font-size: 0.88rem;
            }
            .nav-sidebar .nav-treeview .nav-link {
                font-size: 0.82rem;
            }
        }
        @media (max-width: 767.98px) {
            .brand-link {
                min-height: 60px;
            }
            .main-sidebar .sidebar {
                height: calc(100vh - 60px);
                padding-top: 0.5rem;
            }
            .nav-sidebar .nav-link {
                margin: 4px 8px;
                padding: 8px 10px;
            }
            .main-header .navbar-nav .nav-link span {
                font-size: 0.75rem;
            }
            .table {
                font-size: 0.72rem;
            }
            .btn {
                font-size: 0.7rem;
                padding: 0.3rem 0.5rem;
            }
            .content-header h1 {
                font-size: 1rem;
            }
            .nav-sidebar .nav-link {
                font-size: 0.82rem;
            }
            .nav-sidebar .nav-treeview .nav-link {
                font-size: 0.78rem;
            }
            .btn .btn-text {
                display: none;
            }
            .btn.btn-xs {
                padding: 0.2rem 0.3rem;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
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
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle Menu">
                    <label class="burger" aria-hidden="true">
                        <input id="menuToggle" type="checkbox">
                        <span></span>
                        <span></span>
                        <span></span>
                    </label>
                </a>
            </li>
            <li class="nav-item d-none d-md-flex align-items-center">
                <a href="<?= site_url('admin') ?>" class="topbar-identity text-decoration-none">
                    <img src="<?= esc($logoSrc) ?>" alt="Logo Kabupaten Donggala">
                    <span>
                        <strong>eLabel Donggala</strong>
                        <span>Pemerintah Kabupaten</span>
                    </span>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link position-relative" href="<?= site_url('admin/loans') ?>" title="Notifikasi Peminjaman">
                    <i class="far fa-bell" style="font-size:1rem; line-height:1;"></i>
                    <span class="badge badge-danger navbar-badge" style="font-size:0.6rem; padding:2px 5px; border-radius:999px;">!</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" role="button">
                    <img src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width:28px;height:28px;">
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <div class="dropdown-item p-2">
                        <div class="d-flex align-items-center">
                            <img src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/img/user2-160x160.jpg" class="img-circle elevation-2 mr-2" alt="User Image" style="width:38px;height:38px;">
                            <div>
                                <div class="font-weight-bold">Hallo, <?= esc((string) session()->get('user_name')) ?></div>
                                <div class="text-muted small">Logged in as Admin</div>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item d-flex justify-content-between align-items-center">
                        Edit Profile
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="#" class="dropdown-item d-flex justify-content-between align-items-center">
                        Ubah Password
                        <i class="fas fa-cog"></i>
                    </a>
                    <a href="#" class="dropdown-item d-flex justify-content-between align-items-center">
                        Help
                        <i class="fas fa-question-circle"></i>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="<?= site_url('logout') ?>" method="post" class="px-3 pb-2">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger btn-block btn-sm">Sign Out</button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?= site_url('admin') ?>" class="brand-link">
            <img src="<?= esc($logoSrc) ?>" alt="Logo Kabupaten Donggala" class="brand-image img-circle elevation-3" style="opacity: .96">
            <span class="brand-text font-weight-bold">eLabel<br><small>Kabupaten Donggala</small></span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                    <li class="nav-item">
                        <a href="<?= site_url('admin') ?>" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview <?= in_array(($activeMenu ?? ''), ['boxes', 'boxes_mobil', 'boxes_motor', 'sertifikat_boxes', 'surat_penyerahan_boxes'], true) ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= in_array(($activeMenu ?? ''), ['boxes', 'boxes_mobil', 'boxes_motor', 'sertifikat_boxes', 'surat_penyerahan_boxes'], true) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-box"></i>
                            <p>
                                Data Box
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= site_url('admin/boxes') ?>" class="nav-link <?= ($activeMenu ?? '') === 'boxes' ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Box BPKB</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= site_url('admin/sertifikat-boxes') ?>" class="nav-link <?= ($activeMenu ?? '') === 'sertifikat_boxes' ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Box Sertipikat</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= site_url('admin/surat-penyerahan-boxes') ?>" class="nav-link <?= ($activeMenu ?? '') === 'surat_penyerahan_boxes' ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Box Surat Penyerahan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview <?= in_array(($activeMenu ?? ''), ['bpkb_mobil', 'bpkb_motor', 'bpkb', 'sertifikat', 'surat_penyerahan'], true) ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= in_array(($activeMenu ?? ''), ['bpkb_mobil', 'bpkb_motor', 'bpkb', 'sertifikat', 'surat_penyerahan'], true) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Arsip
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= site_url('admin/bpkb') ?>" class="nav-link <?= in_array(($activeMenu ?? ''), ['bpkb_mobil', 'bpkb_motor', 'bpkb'], true) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data BPKB</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= site_url('admin/sertifikat') ?>" class="nav-link <?= ($activeMenu ?? '') === 'sertifikat' ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Sertifikat Tanah</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= site_url('admin/surat-penyerahan') ?>" class="nav-link <?= ($activeMenu ?? '') === 'surat_penyerahan' ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Surat Penyerahan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview <?= in_array(($activeMenu ?? ''), ['deleted', 'loans'], true) ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= in_array(($activeMenu ?? ''), ['deleted', 'loans'], true) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Pelaporan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= site_url('admin/bpkb-deleted') ?>" class="nav-link <?= ($activeMenu ?? '') === 'deleted' ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>BPKB Keluar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= site_url('admin/loans') ?>" class="nav-link <?= ($activeMenu ?? '') === 'loans' ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Peminjaman</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php if (session()->get('user_role') === 'super_admin'): ?>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/users') ?>" class="nav-link <?= ($activeMenu ?? '') === 'users' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Admin</p>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="main-footer">
        <strong>eLabel Admin Panel</strong>
        <div class="float-right d-none d-sm-inline-block footer-version">
            <b>Version</b> 1.0.0
        </div>
    </footer>

    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.8/js/dataTables.bootstrap4.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('menuToggle');
        var pushmenuLink = document.querySelector('[data-widget="pushmenu"]');
        if (!toggle || !pushmenuLink) {
            return;
        }
        var syncToggle = function () {
            toggle.checked = document.body.classList.contains('sidebar-collapse');
        };
        syncToggle();
        pushmenuLink.addEventListener('click', function () {
            setTimeout(syncToggle, 0);
        });
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
