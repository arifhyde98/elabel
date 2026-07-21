<?= $this->extend('layouts/userlte') ?>

<?= $this->section('title') ?>Dashboard | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Dashboard User</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <p class="mb-1"><strong>Nama:</strong> <?= esc((string) $name) ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= esc((string) $email) ?></p>
                <p class="mb-0"><strong>Role:</strong> <?= esc((string) $role) ?></p>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
