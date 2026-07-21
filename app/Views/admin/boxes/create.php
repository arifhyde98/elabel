<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Tambah Box <?= esc((string) $vehicleLabel) ?> | eLabel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Tambah Box Arsip <?= esc((string) $vehicleLabel) ?></h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('admin/boxes') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="vehicle_type">Jenis Kendaraan</label>
                        <?php if (! empty($vehicleType)): ?>
                            <input type="hidden" name="vehicle_type" value="<?= esc((string) $vehicleType) ?>">
                            <input type="text" class="form-control" value="<?= esc((string) $vehicleLabel) ?>" readonly>
                        <?php else: ?>
                            <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                <option value="">Pilih jenis</option>
                                <option value="R4" <?= old('vehicle_type') === 'R4' ? 'selected' : '' ?>>R4</option>
                                <option value="R2" <?= old('vehicle_type') === 'R2' ? 'selected' : '' ?>>R2</option>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="box_code">Kode Box</label>
                        <?php
                        $oldBoxCode = (string) old('box_code');
                        $defaultPrefix = ! empty($vehicleType) ? (string) ($nextBoxCodes[$vehicleType] ?? ($vehicleType . '-01')) : '';
                        $boxCodeValue = $oldBoxCode !== '' ? $oldBoxCode : $defaultPrefix;
                        ?>
                        <input type="text" name="box_code" id="box_code" class="form-control" value="<?= esc($boxCodeValue) ?>" placeholder="Pilih jenis kendaraan terlebih dahulu" required>
                        <small class="text-muted">Kode box terisi otomatis setelah jenis kendaraan dipilih.</small>
                    </div>
                    <div class="form-group">
                        <label for="location">Lokasi Ruang</label>
                        <input type="text" name="location" id="location" class="form-control" value="<?= esc((string) old('location')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="years">Tahun yang Ditampung</label>
                        <input type="text" name="years" id="years" class="form-control" value="<?= esc((string) old('years')) ?>" placeholder="Contoh: 2021-2022-2023" required>
                        <small class="text-muted">Pisahkan tahun dengan tanda -</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= $vehicleRoute ? site_url('admin/boxes/' . $vehicleRoute) : site_url('admin/boxes') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var vehicleTypeInput = document.getElementById('vehicle_type');
        var boxCodeInput = document.getElementById('box_code');
        var nextBoxCodes = <?= json_encode($nextBoxCodes ?? [], JSON_UNESCAPED_SLASHES) ?>;

        if (!boxCodeInput) {
            return;
        }

        var lastAutoValue = '';
        var buildBoxCode = function (vehicleType) {
            if (!vehicleType) {
                return '';
            }

            return nextBoxCodes[vehicleType] || (vehicleType + '-01');
        };

        var syncBoxCode = function (vehicleType) {
            var nextValue = buildBoxCode(vehicleType);
            var currentValue = boxCodeInput.value.trim();

            if (currentValue === '' || currentValue === lastAutoValue) {
                boxCodeInput.value = nextValue;
                lastAutoValue = nextValue;
            }
        };

        if (vehicleTypeInput) {
            boxCodeInput.readOnly = vehicleTypeInput.value === '';
            syncBoxCode(vehicleTypeInput.value);
            vehicleTypeInput.addEventListener('change', function () {
                boxCodeInput.readOnly = vehicleTypeInput.value === '';
                syncBoxCode(vehicleTypeInput.value);
            });
            return;
        }

        lastAutoValue = boxCodeInput.value.trim();
    });
</script>
<?= $this->endSection() ?>
