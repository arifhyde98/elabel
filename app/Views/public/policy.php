<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi Kebijakan | ArsipKu</title>
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
        .glass-card li {
            color: #bfdbfe !important;
        }
        .btn-back {
            border: 1px solid rgba(216, 169, 40, 0.7);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }
        .btn-back:hover {
            color: #ffffff;
            background: rgba(216, 169, 40, 0.18);
        }
    </style>
</head>
<body>
<main class="container py-4">
    <div class="card glass-card">
        <div class="card-body">
            <h1 class="h4">Informasi Kebijakan</h1>
            <p class="text-muted">Ketentuan umum penggunaan layanan ArsipKu Kabupaten Donggala.</p>

            <ul class="pl-3">
                <li>Pengecekan dokumen pada halaman publik hanya untuk membantu identifikasi awal.</li>
                <li>Pengajuan permintaan file scan BPKB akan diverifikasi oleh admin Bidang Aset.</li>
                <li>Pemohon wajib mengisi identitas dan nomor kontak yang benar.</li>
                <li>Dokumen yang berstatus tidak tersedia tidak dapat diajukan untuk permintaan scan.</li>
                <li>Keputusan persetujuan, penolakan, dan pengembalian mengikuti kebijakan pengelolaan aset daerah.</li>
            </ul>

            <a href="<?= site_url('/') ?>" class="btn btn-back btn-sm">Kembali</a>
        </div>
    </div>
</main>
</body>
</html>
