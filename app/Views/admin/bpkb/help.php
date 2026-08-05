<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Bantuan | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Bantuan</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5>eLabel Donggala</h5>
                <p class="text-muted mb-4">Gunakan halaman ini sebagai pintasan bantuan penggunaan sistem.</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fas fa-search mr-1"></i> Pencarian Dokumen</h6>
                            <p class="mb-0 text-muted">Gunakan kolom pencarian pada tabel untuk mencari nomor polisi, nomor dokumen, NIBAR, box, tahun, atau data terkait.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fas fa-file-import mr-1"></i> Import Data</h6>
                            <p class="mb-0 text-muted">Gunakan format import yang tersedia pada masing-masing menu agar struktur kolom sesuai dengan sistem.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fas fa-box mr-1"></i> Data Box</h6>
                            <p class="mb-0 text-muted">Pastikan box, tahun, jenis kendaraan, dan lokasi sudah benar sebelum menambahkan dokumen arsip.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fas fa-user-cog mr-1"></i> Akun</h6>
                            <p class="mb-0 text-muted">Ubah nama, email, password, dan foto profil melalui menu Edit Profile pada pojok kanan atas.</p>
                        </div>
                    </div>
                </div>

                <a href="<?= site_url('admin') ?>" class="btn btn-secondary btn-sm">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
