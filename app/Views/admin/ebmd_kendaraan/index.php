<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Kendaraan E-BMD | Master Data | ArsipKu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark"><i class="fas fa-car text-warning mr-2"></i>Master Data Kendaraan E-BMD</h1>
            <small class="text-muted">Kelola data referensi aset kendaraan terintegrasi E-BMD</small>
        </div>
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
            <li class="breadcrumb-item">Master Data</li>
            <li class="breadcrumb-item active">Kendaraan E-BMD</li>
        </ol>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h3 class="card-title text-dark font-weight-bold mb-0">
                    <i class="fas fa-list mr-1 text-warning"></i> Data Kendaraan E-BMD
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0 rounded-lg">
                    <i class="fas fa-info-circle mr-2"></i>
                    Modul <strong>Kendaraan E-BMD</strong> siap digunakan untuk integrasi master data aset kendaraan E-BMD.
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
