<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Pengajuan | ArsipKu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            min-height: 100vh;
            color: #ffffff;
            background:
                linear-gradient(135deg, rgba(7, 21, 47, 0.98) 0%, rgba(14, 45, 99, 0.96) 58%, rgba(29, 78, 216, 0.92) 100%),
                repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0 1px, transparent 1px 18px);
            font-family: "Poppins", "Segoe UI", sans-serif;
        }
        main {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .glass-card {
            width: 100%;
            border: 1px solid rgba(216, 169, 40, 0.32);
            border-top: 3px solid #d8a928;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.11);
            color: #ffffff;
            backdrop-filter: blur(15px);
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
        }
        .glass-card h1 {
            color: #d8a928;
            font-weight: 700;
        }
        .glass-card .text-muted,
        .glass-card dt {
            color: #bfdbfe !important;
        }
        .glass-card dd {
            color: #ffffff;
        }
        .form-control {
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 12px 0 0 12px;
        }
        .btn-primary {
            background: #1d4ed8;
            border-color: #d8a928;
            color: #fef3c7;
        }
        .btn-back {
            border: 1px solid rgba(216, 169, 40, 0.7);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }
        .btn-back:hover,
        .btn-primary:hover {
            color: #ffffff;
            background: rgba(216, 169, 40, 0.18);
        }
        hr {
            border-top-color: rgba(216, 169, 40, 0.26);
        }
    </style>
</head>
<body>
<main class="container py-4">
    <div class="card glass-card">
        <div class="card-body">
            <h1 class="h4">Status Pengajuan</h1>
            <p class="text-muted">Masukkan nomor pengajuan yang diterima setelah mengirim formulir permintaan scan.</p>

            <form action="<?= site_url('status-pengajuan') ?>" method="get" class="mb-3">
                <div class="input-group">
                    <input type="text" name="code" class="form-control" value="<?= esc((string) $code) ?>" placeholder="Contoh: L-20260805-000001" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Cek</button>
                    </div>
                </div>
            </form>

            <?php if (! empty($error)): ?>
                <div class="alert alert-warning"><?= esc((string) $error) ?></div>
            <?php elseif (! empty($item)): ?>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nomor Pengajuan</dt>
                    <dd class="col-sm-8"><?= esc((string) $code) ?></dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8"><strong><?= esc((string) $item['status']) ?></strong></dd>
                    <dt class="col-sm-4">Nama Pemohon</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['requester_name'] ?? '-')) ?></dd>
                    <dt class="col-sm-4">No. Polisi</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['plate_number'] ?? '-')) ?></dd>
                    <dt class="col-sm-4">Box</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['box_code'] ?? '-')) ?></dd>
                    <dt class="col-sm-4">Tanggal Pengajuan</dt>
                    <dd class="col-sm-8"><?= esc((string) ($item['requested_at'] ?? '-')) ?></dd>
                </dl>
            <?php endif; ?>

            <hr>
            <a href="<?= site_url('/') ?>" class="btn btn-back btn-sm">Kembali</a>
        </div>
    </div>
</main>
</body>
</html>
