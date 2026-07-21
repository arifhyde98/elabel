<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eLabel 2025 - Pemerintah Kabupaten Donggala</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #07152f;
            --main-bg-gradient: linear-gradient(135deg, #07152f 0%, #0e2d63 58%, #1d4ed8 100%);
            --text-main: #ffffff;
            --text-muted: #bfdbfe;
            --text-highlight: #d8a928;
            --glass-bg: rgba(255, 255, 255, 0.11);
            --glass-border: rgba(216, 169, 40, 0.32);
            --glass-blur: blur(15px);
            --btn-action-bg: #1d4ed8;
            --btn-action-border: #d8a928;
            --accent-blue: #1d4ed8;
            --accent-green: #22c55e;
            --surface: #f8fafc;
            --slate: #64748b;
            --landing-card-width: 520px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-main);
            background:
                linear-gradient(135deg, rgba(7, 21, 47, 0.98) 0%, rgba(14, 45, 99, 0.96) 58%, rgba(29, 78, 216, 0.92) 100%),
                repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0 1px, transparent 1px 18px);
            overflow-x: hidden;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .container_fluida {
            width: 100%;
            padding: 0 40px;
        }

        .icon-small { width: 20px; height: 20px; }
        .icon-med { width: 30px; height: 30px; }
        .svg-icon {
            width: 20px;
            height: 20px;
            display: inline-block;
            flex: 0 0 auto;
        }
        .svg-icon-med {
            width: 30px;
            height: 30px;
            display: inline-block;
            flex: 0 0 auto;
        }

        .top-bar {
            background:
                linear-gradient(90deg, #07152f 0%, #0e2d63 62%, #1d4ed8 100%);
            height: 60px;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 10;
            border-bottom: 4px solid var(--text-highlight);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
        }

        .top-bar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-text {
            font-size: 14px;
            font-weight: 500;
        }

        .btn-login-admin {
            background: transparent;
            border: 1.5px solid var(--text-highlight);
            color: var(--text-highlight);
            padding: 8px 18px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
        }

        .btn-login-admin:hover {
            background: rgba(255, 215, 0, 0.1);
        }

        .main-content {
            position: relative;
            padding-top: 40px;
            padding-bottom: 100px;
            min-height: calc(100vh - 60px);
            background:
                linear-gradient(135deg, rgba(7, 21, 47, 0.92), rgba(14, 45, 99, 0.88) 58%, rgba(29, 78, 216, 0.82)),
                repeating-linear-gradient(90deg, transparent 0 34px, rgba(255, 255, 255, 0.045) 34px 35px);
        }

        .map-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }

        .map-bg svg {
            width: 100%;
            height: 100%;
        }

        .map-path {
            fill: none;
            stroke: var(--text-main);
            stroke-width: 1;
        }

        .main-layout {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: minmax(0, var(--landing-card-width)) minmax(420px, var(--landing-card-width));
            column-gap: 80px;
            row-gap: 28px;
            align-items: start;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .left-section {
            grid-column: 1;
        }

        .agency-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .agency-logo {
            width: 70px;
            height: auto;
            background: #fff;
            border: 2px solid rgba(216, 169, 40, 0.7);
            border-radius: 14px;
            padding: 4px;
            filter: drop-shadow(0 8px 14px rgba(0,0,0,0.22));
        }

        .agency-text h1 {
            font-size: 20px;
            color: var(--text-highlight);
            font-weight: 700;
            margin-bottom: 2px;
        }

        .agency-text p {
            font-size: 12px;
            font-weight: 300;
            color: var(--text-main);
            line-height: 1.4;
        }

        .hero-titles h2 {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 5px;
        }

        .hero-titles .subtitle {
            font-size: 16px;
            font-weight: 300;
            color: var(--text-muted);
            margin-top: 15px;
        }

        .document-summary {
            grid-column: 1;
            width: 100%;
            max-width: var(--landing-card-width);
            margin-top: 0;
            padding: 30px;
            border: 1px solid var(--glass-border);
            border-top: 3px solid var(--text-highlight);
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.11);
            backdrop-filter: var(--glass-blur);
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
        }

        .document-summary h3 {
            margin: 0 0 14px;
            color: var(--text-main);
            font-size: 18px;
            font-weight: 700;
        }

        .summary-grid {
            display: grid;
            gap: 10px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 58px;
            padding: 12px 14px;
            border: 1px solid rgba(226, 232, 240, 0.18);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.09);
        }

        .summary-label {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
        }

        .summary-value {
            color: #fde68a;
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
        }

        .right-section {
            grid-column: 2;
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: stretch;
        }

        .floating-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: var(--glass-blur);
            padding: 12px 25px;
            border-radius: 30px;
            color: var(--text-main);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .floating-btn:hover {
            background: rgba(216, 169, 40, 0.16);
            transform: translateY(-2px);
        }

        .search-card {
            grid-column: 2;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: var(--glass-blur);
            width: 100%;
            max-width: var(--landing-card-width);
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
        }

        .doc-toggles {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .toggle-item {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid transparent;
            padding: 15px 20px;
            border-radius: 15px;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-family: inherit;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            min-height: 62px;
            overflow: hidden;
        }

        .toggle-item input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .toggle-item .icon-vehicle,
        .toggle-item .icon-map {
            position: absolute;
            right: 20px;
            opacity: 0.8;
        }

        .toggle-item .icon-vehicle { width: 60px; height: auto; bottom: -5px; }
        .toggle-item .icon-map { width: 40px; height: auto; top: 10px; }

        .toggle-item#btn-bpkb.active,
        .toggle-item#btn-tanah.active {
            background-color: var(--accent-blue);
            box-shadow: inset 0 0 15px rgba(0,0,0,0.2);
        }

        .toggle-item#btn-tanah {
            justify-content: flex-start;
        }

        .status-dot {
            width: 15px;
            height: 15px;
            background-color: var(--text-main);
            border-radius: 50%;
            margin-right: 5px;
            flex: 0 0 auto;
        }

        .search-field {
            background: rgba(255, 255, 255, 0.94);
            border-radius: 15px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            margin-bottom: 25px;
            height: 60px;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
        }

        .icon-search {
            width: 24px;
            height: 24px;
            margin-right: 15px;
        }

        .search-field input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 16px;
            color: #333;
            min-width: 0;
        }

        .search-field input::placeholder {
            color: #888;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
        }

        .info-text {
            flex: 1;
            font-size: 13px;
            font-weight: 300;
            line-height: 1.5;
            color: #bfdbfe;
        }

        .btn-cek {
            background-color: var(--btn-action-bg);
            border: 1px solid var(--btn-action-border);
            color: var(--text-highlight);
            padding: 15px 30px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .btn-cek:hover {
            background-color: #0e2d63;
            transform: scale(1.03);
        }

        .alerts-wrap,
        .result-section {
            position: relative;
            z-index: 4;
        }

        .alerts-wrap {
            margin-bottom: 20px;
        }

        .alert {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 14px;
            color: #0f172a;
            margin-bottom: 12px;
            padding: 12px 14px;
        }

        .result-section {
            margin-top: 25px;
            color: var(--text-main);
            width: 100%;
            max-width: var(--landing-card-width);
            padding: 0;
            margin-right: calc((100% - 1200px) / 2 + 20px);
            margin-left: auto;
        }

        .result-panel {
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid var(--glass-border);
            border-top: 3px solid var(--text-highlight);
            border-radius: 25px;
            backdrop-filter: var(--glass-blur);
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
            padding: 30px;
        }

        .result-panel h3 {
            margin-bottom: 18px;
            color: var(--text-main);
        }

        .result-list {
            display: grid;
            gap: 14px;
        }

        .result-card {
            background: #fff;
            border: 1px solid #dbe4ef;
            border-radius: 16px;
            padding: 16px;
        }

        .result-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .result-title {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.25;
        }

        .status-badge {
            background: #e9f2fb;
            color: #083c6b;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            white-space: nowrap;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .detail-item {
            min-width: 0;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            padding: 10px;
        }

        .detail-item span {
            display: block;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .detail-item strong {
            display: block;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.35;
            overflow-wrap: anywhere;
            word-break: break-word;
            max-height: 4.1em;
            overflow: hidden;
        }

        .btn-secondary,
        .btn-submit-loan {
            border: 0;
            border-radius: 12px;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
            min-height: 42px;
            padding: 0 16px;
        }

        .btn-secondary {
            background: #e9f2fb;
            color: #083c6b;
        }

        .btn-submit-loan {
            background: #083c6b;
            color: #fff;
        }

        .loan-form {
            display: none;
        }

        .loan-form.is-open {
            display: block;
        }

        .loan-modal {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .loan-modal.is-open {
            display: flex;
        }

        .loan-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(7, 21, 47, 0.72);
        }

        .loan-modal-dialog {
            position: relative;
            width: min(680px, 100%);
            max-height: min(90vh, 760px);
            margin: auto;
            overflow-y: auto;
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #dbe4ef;
            border-top: 4px solid var(--text-highlight);
            border-radius: 18px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.36);
        }

        .loan-modal-header,
        .loan-modal-body,
        .loan-modal-footer {
            padding: 18px 20px;
        }

        .loan-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .loan-modal-title {
            margin: 0 0 4px;
            font-size: 18px;
            line-height: 1.25;
        }

        .loan-modal-subtitle {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }

        .loan-modal-close {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 10px;
            background: #f1f5f9;
            color: #334155;
            cursor: pointer;
            font: inherit;
            font-size: 22px;
            line-height: 1;
        }

        .loan-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .body-modal-open {
            overflow: hidden;
        }

        .loan-form .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .loan-form label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .loan-form input,
        .loan-form textarea {
            width: 100%;
            border: 1px solid #d9e2ef;
            border-radius: 12px;
            font: inherit;
            padding: 10px 12px;
        }

        .loan-form textarea {
            min-height: 80px;
            resize: vertical;
        }

        .empty-state {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 14px;
            color: #7c2d12;
            font-weight: 700;
            padding: 16px;
        }

        @media (max-width: 992px) {
            .main-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .left-section,
            .right-section,
            .document-summary,
            .search-card {
                grid-column: 1;
            }
            .right-section {
                width: 100%;
                flex-direction: row;
                justify-content: center;
            }
            .hero-titles h2 { font-size: 28px; }
            .document-summary,
            .search-card {
                max-width: 100%;
            }
            .result-section {
                max-width: 100%;
                padding: 0 20px;
                margin-right: auto;
            }
        }

        @media (max-width: 768px) {
            .container_fluida { padding: 0 20px; }
            .agency-info { gap: 10px; flex-direction: column; text-align: center; }
            .doc-toggles { flex-direction: column; }
            .card-footer { flex-direction: column; text-align: center; gap: 20px; }
            .right-section { flex-direction: column; align-items: center; }
        }
    </style>
</head>
<body>
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

    <header class="top-bar">
        <div class="container_fluida top-bar-content">
            <span class="status-text">ANDA SEDANG LOGIN ELABEL 2025</span>
            <a class="btn-login-admin" href="<?= site_url('login') ?>">
                <img src="https://img.icons8.com/m_outlined/50/FFFFFF/user-male-circle.png" alt="user icon" class="icon-small">
                Login Admin
            </a>
        </div>
    </header>

    <main class="main-content">
        <div class="map-bg">
            <svg viewBox="0 0 1000 600" xmlns="http://www.w3.org/2000/svg">
                <path class="map-path" d="M150,100 C200,50 300,150 400,100 S550,200 600,150 S750,250 800,200 S900,100 950,150 L950,550 L50,550 Z" />
            </svg>
        </div>

        <div class="container main-layout">
            <div class="left-section">
                <div class="agency-info">
                    <img src="<?= esc($logoSrc) ?>" alt="Logo Donggala" class="agency-logo">
                    <div class="agency-text">
                        <h1>Pemerintah Kabupaten Donggala</h1>
                        <p>Badan Pengelolaan Keuangan dan Aset Daerah</p>
                        <p>Bidang Aset</p>
                    </div>
                </div>

                <div class="hero-titles">
                    <h2>eLabel 2025: Akses Cepat</h2>
                    <h2>Pengelolaan Aset Donggala</h2>
                    <p class="subtitle">Pengecekan sebelum login</p>
                </div>
            </div>

            <aside class="right-section">
                <a href="#" class="floating-btn">
                    Informasi Kebijakan
                    <img src="https://img.icons8.com/m_sharp/50/FFFFFF/info.png" alt="info" class="icon-med">
                </a>
                <a href="#" class="floating-btn">
                    Status Pengajuan
                    <svg class="svg-icon-med" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm0 2v14h10V5H7Zm2 3h6v2H9V8Zm0 4h6v2H9v-2Zm0 4h4v2H9v-2Z"/>
                    </svg>
                </a>
            </aside>

            <div class="document-summary">
                <h3>Jumlah Dokumen Dalam Sistem</h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="summary-label">BPKB</span>
                        <span class="summary-value"><?= esc((string) ($bpkbCount ?? 0)) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Sertipikat Tanah</span>
                        <span class="summary-value"><?= esc((string) ($sertifikatCount ?? 0)) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Surat Penyerahan</span>
                        <span class="summary-value"><?= esc((string) ($suratPenyerahanCount ?? 0)) ?></span>
                    </div>
                </div>
            </div>

            <form class="search-card" action="<?= site_url('/') ?>" method="get">
                <div class="doc-toggles">
                    <label class="toggle-item <?= ($documentType ?? 'bpkb') === 'bpkb' ? 'active' : '' ?>" id="btn-bpkb">
                        <input type="radio" name="type" value="bpkb" <?= ($documentType ?? 'bpkb') === 'bpkb' ? 'checked' : '' ?>>
                        <svg class="svg-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="M5 7.5 6.4 4h11.2L19 7.5l1.6 1.2c.25.19.4.49.4.8V17a1 1 0 0 1-1 1h-1v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5V18H8v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5V18H4a1 1 0 0 1-1-1V9.5c0-.31.15-.61.4-.8L5 7.5Zm2.75-1.5-.8 2h10.1l-.8-2h-8.5ZM6.5 15A1.5 1.5 0 1 0 6.5 12a1.5 1.5 0 0 0 0 3Zm11 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM10 12v2h4v-2h-4Z"/>
                        </svg>
                        <span>BPKB</span>
                        <img src="https://img.icons8.com/m_outlined/50/FFFFFF/car.png" alt="Car" class="icon-vehicle">
                    </label>
                    <label class="toggle-item <?= ($documentType ?? '') === 'sertifikat' ? 'active' : '' ?>" id="btn-tanah">
                        <input type="radio" name="type" value="sertifikat" <?= ($documentType ?? '') === 'sertifikat' ? 'checked' : '' ?>>
                        <div class="status-dot"></div>
                        <span>Sertifikat Tanah</span>
                        <img src="https://img.icons8.com/m_outlined/50/FFFFFF/region-code.png" alt="Map" class="icon-map">
                    </label>
                </div>

                <div class="search-field">
                    <img src="https://img.icons8.com/m_outlined/50/888888/search.png" alt="search" class="icon-search">
                    <input type="text" id="q" name="q" value="<?= esc((string) ($query ?? '')) ?>" placeholder="Masukkan Nomor Polisi (misal: DN 1234 XY)" required>
                </div>

                <div class="card-footer">
                    <p class="info-text">
                        Masukkan kriteria pencarian dokumen. Detail akan tampil untuk pengajuan peminjaman dari hasil pengecekan.
                    </p>
                    <button class="btn-cek" type="submit">
                        <img src="https://img.icons8.com/m_outlined/50/FFD700/scan-stock.png" alt="scan" class="icon-small">
                        Cek Dokumen
                    </button>
                </div>
            </form>
        </div>

        <div class="container alerts-wrap" id="umpan-balik">
            <?= view('partials/alerts') ?>
            <?php $successDetail = session()->getFlashdata('success_detail'); ?>
            <?php if (! empty($successDetail) && is_array($successDetail)): ?>
                <div class="alert alert-success">
                    <strong>Nomor Pengajuan:</strong> <?= esc((string) ($successDetail['code'] ?? '')) ?><br>
                    <span>Nama: <?= esc((string) ($successDetail['name'] ?? '-')) ?> - No. HP: <?= esc((string) ($successDetail['phone'] ?? '-')) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (($query ?? '') !== ''): ?>
            <section class="container result-section" id="hasil-pengecekan">
                <div class="result-panel">
                    <h3>Hasil Pengecekan</h3>
                    <?php if (($documentType ?? 'bpkb') === 'bpkb'): ?>
                        <?php if (empty($bpkbResults)): ?>
                            <div class="empty-state">Data BPKB dengan nomor polisi tersebut tidak ditemukan.</div>
                        <?php else: ?>
                            <div class="result-list">
                                <?php foreach ($bpkbResults as $item): ?>
                                    <?php $loanFormId = 'loan-form-bpkb-' . (int) $item['id']; ?>
                                    <div class="result-card">
                                        <div class="result-head">
                                            <div>
                                                <div class="result-title"><?= esc((string) $item['plate_number']) ?></div>
                                                <small>Dokumen BPKB</small>
                                            </div>
                                            <span class="status-badge"><?= esc((string) $item['status']) ?></span>
                                        </div>
                                        <div class="detail-grid">
                                            <div class="detail-item"><span>Nomor BPKB</span><strong><?= esc((string) ($item['no_bpkb'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Tahun</span><strong><?= esc((string) $item['year']) ?></strong></div>
                                            <div class="detail-item"><span>Jenis</span><strong><?= esc((string) $item['vehicle_type']) ?></strong></div>
                                            <div class="detail-item"><span>Box</span><strong><?= esc((string) ($item['box_code'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Merek/Tipe</span><strong><?= esc(trim((string) ($item['merek'] ?? '') . ' ' . (string) ($item['tipe'] ?? '')) ?: '-') ?></strong></div>
                                            <div class="detail-item"><span>Isi Silinder</span><strong><?= esc((string) ($item['isi_silinder'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Warna</span><strong><?= esc((string) ($item['warna'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Pengguna</span><strong><?= esc((string) ($item['pengguna'] ?? '-')) ?></strong></div>
                                        </div>
                                        <?php if (($item['status'] ?? '') === 'Tersedia'): ?>
                                            <button class="btn-secondary" type="button" data-open-loan-modal="<?= esc($loanFormId) ?>">Peminjaman</button>
                                            <div id="<?= esc($loanFormId) ?>" class="loan-modal" aria-hidden="true">
                                                <div class="loan-modal-backdrop" data-close-loan-modal></div>
                                                <div class="loan-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="<?= esc($loanFormId) ?>-title">
                                                    <form action="<?= site_url('loan-request') ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="bpkb_id" value="<?= (int) $item['id'] ?>">
                                                        <input type="hidden" name="return_url" value="<?= esc(current_url() . (! empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '') . '#umpan-balik') ?>">
                                                        <div class="loan-modal-header">
                                                            <div>
                                                                <h3 class="loan-modal-title" id="<?= esc($loanFormId) ?>-title">Form Peminjaman BPKB</h3>
                                                                <div class="loan-modal-subtitle"><?= esc((string) $item['plate_number']) ?> - Box <?= esc((string) ($item['box_code'] ?? '-')) ?></div>
                                                            </div>
                                                            <button type="button" class="loan-modal-close" data-close-loan-modal aria-label="Tutup">&times;</button>
                                                        </div>
                                                        <div class="loan-modal-body loan-form is-open">
                                                            <div class="row">
                                                                <div>
                                                                    <label>Nama Pemohon</label>
                                                                    <input type="text" name="requester_name" value="<?= esc((string) old('requester_name')) ?>" required>
                                                                </div>
                                                                <div>
                                                                    <label>No. HP</label>
                                                                    <input type="text" name="requester_phone" value="<?= esc((string) old('requester_phone')) ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div>
                                                                    <label>Email (opsional)</label>
                                                                    <input type="email" name="requester_email" value="<?= esc((string) old('requester_email')) ?>">
                                                                </div>
                                                                <div>
                                                                    <label>Instansi (opsional)</label>
                                                                    <input type="text" name="requester_org" value="<?= esc((string) old('requester_org')) ?>">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div>
                                                                    <label>Alamat (opsional)</label>
                                                                    <input type="text" name="requester_address" value="<?= esc((string) old('requester_address')) ?>">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div>
                                                                    <label>Keperluan/Catatan</label>
                                                                    <textarea name="requester_note"><?= esc((string) old('requester_note')) ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="loan-modal-footer">
                                                            <button class="btn-secondary" type="button" data-close-loan-modal>Batal</button>
                                                            <button class="btn-submit-loan" type="submit">Kirim Pengajuan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn-secondary" type="button" disabled>Dokumen Tidak Tersedia</button>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (empty($sertifikatResults)): ?>
                            <div class="empty-state">Data sertipikat tanah dengan status penggunaan tersebut tidak ditemukan.</div>
                        <?php else: ?>
                            <div class="result-list">
                                <?php foreach ($sertifikatResults as $item): ?>
                                    <div class="result-card">
                                        <div class="result-head">
                                            <div>
                                                <div class="result-title"><?= esc((string) ($item['no_sertipikat'] ?? '-')) ?></div>
                                                <small>Dokumen Sertipikat Tanah</small>
                                            </div>
                                            <span class="status-badge"><?= esc((string) ($item['status_penggunaan'] ?? '-')) ?></span>
                                        </div>
                                        <div class="detail-grid">
                                            <div class="detail-item"><span>NIBAR</span><strong><?= esc((string) ($item['nibar'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Nama Pemilik</span><strong><?= esc((string) ($item['nama_pemilik'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Luas</span><strong><?= esc((string) ($item['luas'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Lokasi</span><strong><?= esc((string) ($item['lokasi'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Box</span><strong><?= esc((string) ($item['box_code'] ?? '-')) ?></strong></div>
                                            <div class="detail-item"><span>Dinas</span><strong><?= esc((string) ($item['dinas'] ?? '-')) ?></strong></div>
                                        </div>
                                        <?php $sertifikatInfoId = 'loan-form-sertifikat-' . (int) $item['id']; ?>
                                        <button class="btn-secondary" type="button" data-open-loan-modal="<?= esc($sertifikatInfoId) ?>">Peminjaman</button>
                                        <div id="<?= esc($sertifikatInfoId) ?>" class="loan-modal" aria-hidden="true">
                                            <div class="loan-modal-backdrop" data-close-loan-modal></div>
                                            <div class="loan-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="<?= esc($sertifikatInfoId) ?>-title">
                                                <div class="loan-modal-header">
                                                    <div>
                                                        <h3 class="loan-modal-title" id="<?= esc($sertifikatInfoId) ?>-title">Peminjaman Sertipikat Tanah</h3>
                                                        <div class="loan-modal-subtitle"><?= esc((string) ($item['no_sertipikat'] ?? '-')) ?></div>
                                                    </div>
                                                    <button type="button" class="loan-modal-close" data-close-loan-modal aria-label="Tutup">&times;</button>
                                                </div>
                                                <div class="loan-modal-body">
                                                    <div class="empty-state">
                                                        Pengajuan peminjaman sertipikat tanah belum tersedia secara online. Silakan hubungi admin Bidang Aset dengan membawa informasi nomor sertipikat ini.
                                                    </div>
                                                </div>
                                                <div class="loan-modal-footer">
                                                    <button class="btn-secondary" type="button" data-close-loan-modal>Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <script>
        (function () {
            var input = document.getElementById('q');
            var radios = document.querySelectorAll('input[name="type"]');

            var syncSearch = function () {
                var selected = document.querySelector('input[name="type"]:checked');
                var isSertifikat = selected && selected.value === 'sertifikat';
                input.placeholder = isSertifikat ? 'Masukkan Status Penggunaan' : 'Masukkan Nomor Polisi (misal: DN 1234 XY)';

                document.querySelectorAll('.toggle-item').forEach(function (item) {
                    var radio = item.querySelector('input[type="radio"]');
                    item.classList.toggle('active', radio && radio.checked);
                });
            };

            radios.forEach(function (radio) {
                radio.addEventListener('change', syncSearch);
            });
            syncSearch();

            function closeLoanModal(modal) {
                if (!modal) {
                    return;
                }
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('body-modal-open');
            }

            function openLoanModal(modal) {
                if (!modal) {
                    return;
                }
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('body-modal-open');
                var firstInput = modal.querySelector('input:not([type="hidden"]), textarea, button');
                if (firstInput) {
                    firstInput.focus();
                }
            }

            document.querySelectorAll('[data-open-loan-modal]').forEach(function (button) {
                button.addEventListener('click', function () {
                    openLoanModal(document.getElementById(button.getAttribute('data-open-loan-modal')));
                });
            });

            document.querySelectorAll('[data-close-loan-modal]').forEach(function (button) {
                button.addEventListener('click', function () {
                    closeLoanModal(button.closest('.loan-modal'));
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') {
                    return;
                }
                document.querySelectorAll('.loan-modal.is-open').forEach(closeLoanModal);
            });
        })();
    </script>
</body>
</html>
