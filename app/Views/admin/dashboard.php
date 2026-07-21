<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>
Admin Dashboard | eLabel
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dash-hero {
        background:
            linear-gradient(135deg, rgba(7, 21, 47, 0.98) 0%, rgba(14, 45, 99, 0.96) 58%, rgba(29, 78, 216, 0.92) 100%),
            repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0 1px, transparent 1px 18px);
        border-radius: 8px;
        border: 1px solid rgba(216, 169, 40, 0.32);
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.12);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .dash-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(90deg, rgba(216, 169, 40, 0.22), transparent 26%),
            repeating-linear-gradient(90deg, transparent 0 34px, rgba(255, 255, 255, 0.06) 34px 35px);
        pointer-events: none;
    }
    .dash-hero > * {
        position: relative;
        z-index: 1;
    }
    .dash-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.42rem;
        margin-bottom: 0.45rem;
        color: #fde68a;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dash-hero h2 {
        font-size: 1.18rem;
        margin: 0;
        color: #ffffff;
        font-weight: 700;
    }
    .dash-hero p {
        margin: 0;
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.78rem;
    }
    .dash-chip {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(253, 230, 138, 0.46);
        color: #fde68a;
        border-radius: 8px;
        padding: 0.45rem 0.7rem;
        font-size: 0.7rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .dash-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
    }
    .dash-card {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.06);
        padding: 16px;
        border-top: 3px solid #d8a928;
    }
    .dash-card h3 {
        font-size: 0.9rem;
        margin: 0 0 10px;
        color: #0f172a;
        font-weight: 700;
    }
    .dash-search {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.35rem 0.7rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.04);
        max-width: 280px;
        width: 100%;
    }
    .dash-search i {
        color: #94a3b8;
        font-size: 0.85rem;
    }
    .dash-search .form-control {
        border: 0;
        padding: 0;
        height: auto;
        font-size: 0.78rem;
        background: transparent;
    }
    .dash-search .form-control:focus {
        box-shadow: none;
    }
    .dash-statlist {
        display: grid;
        gap: 7px;
    }
    .dash-stat {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        padding: 7px 10px;
        background: linear-gradient(90deg, #f8fafc, #eff6ff);
    }
    .dash-stat .label {
        font-size: 0.68rem;
        color: #64748b;
        font-weight: 600;
    }
    .dash-stat .value {
        font-size: 0.95rem;
        color: #1d4ed8;
        font-weight: 700;
    }
    .dash-mini {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 14px;
    }
    .mini-card {
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .mini-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 0.9rem;
    }
    .mini-info .mini-label {
        font-size: 0.7rem;
        color: #64748b;
        font-weight: 600;
    }
    .mini-info .mini-value {
        font-size: 0.9rem;
        color: #0f172a;
        font-weight: 700;
    }
    .chart-box {
        height: 200px;
        border-radius: 8px;
        background:
            linear-gradient(180deg, #eff6ff 0%, #ffffff 72%),
            repeating-linear-gradient(90deg, rgba(29, 78, 216, 0.04) 0 1px, transparent 1px 18px);
        border: 1px solid #e2e8f0;
        display: grid;
        place-items: center;
    }
    .chart-legend {
        display: flex;
        gap: 12px;
        margin-top: 10px;
        font-size: 0.7rem;
        color: #64748b;
    }
    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }
    .overview-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .overview-item span {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
    }
    .overview-bar {
        height: 6px;
        border-radius: 8px;
        background: #e2e8f0;
        overflow: hidden;
        margin-top: 4px;
    }
    .overview-bar > div {
            height: 100%;
        border-radius: 8px;
    }
    .activity-list {
        position: relative;
        display: grid;
        gap: 0;
        max-height: 390px;
        overflow-y: auto;
        padding: 2px 2px 2px 0;
    }
    .activity-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 10px;
    }
    .activity-toolbar h3 {
        margin: 0;
    }
    .activity-cleanup {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }
    .activity-cleanup button {
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fff1f2;
        color: #b91c1c;
        cursor: pointer;
        font-size: 0.68rem;
        font-weight: 700;
        height: 30px;
        width: 30px;
        padding: 0;
    }
    .activity-cleanup button:hover {
        background: #fee2e2;
    }
    .activity-list::before {
        content: "";
        position: absolute;
        top: 8px;
        bottom: 8px;
        left: 17px;
        width: 2px;
        background: linear-gradient(180deg, #d8a928, #bfdbfe);
        border-radius: 999px;
    }
    .activity-item {
        position: relative;
        display: grid;
        grid-template-columns: 34px minmax(0, 1fr);
        gap: 12px;
        padding: 0 0 12px;
    }
    .activity-item:last-child {
        padding-bottom: 0;
    }
    .activity-icon {
        position: relative;
        z-index: 1;
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        color: #ffffff;
        font-size: 0.82rem;
        border: 3px solid #ffffff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14);
    }
    .activity-content {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.04);
    }
    .activity-icon.create,
    .activity-icon.import,
    .activity-icon.restore {
        background: #16a34a;
    }
    .activity-icon.update {
        background: #1d4ed8;
    }
    .activity-icon.delete {
        background: #dc2626;
    }
    .activity-icon.permanent_delete {
        background: #7f1d1d;
    }
    .activity-icon.default {
        background: #64748b;
    }
    .activity-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 3px;
    }
    .activity-user {
        color: #0f172a;
        font-size: 0.78rem;
        font-weight: 700;
        line-height: 1.25;
    }
    .activity-time {
        color: #1d4ed8;
        font-size: 0.66rem;
        font-weight: 600;
        white-space: nowrap;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        background: #eff6ff;
        padding: 0.18rem 0.38rem;
    }
    .activity-desc {
        color: #475569;
        font-size: 0.72rem;
        line-height: 1.35;
        margin: 0 0 6px;
    }
    .activity-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .activity-pill {
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.62rem;
        font-weight: 700;
        padding: 0.2rem 0.42rem;
        text-transform: uppercase;
    }
    .activity-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        color: #64748b;
        font-size: 0.76rem;
        padding: 14px;
        text-align: center;
        background: #f8fafc;
    }
    @media (max-width: 991.98px) {
        .dash-grid {
            grid-template-columns: 1fr;
        }
        .dash-mini {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 575.98px) {
        .dash-hero {
            align-items: flex-start;
            flex-direction: column;
        }
        .dash-chip {
            white-space: normal;
        }
        .dash-mini {
            grid-template-columns: 1fr;
        }
    }
</style>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1>Dashboard</h1>
            </div>
            <div class="col-sm-6 d-flex justify-content-sm-end mt-2 mt-sm-0">
                <form class="dash-search" action="<?= site_url('admin/bpkb') ?>" method="get">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" class="form-control" placeholder="Cari dokumen" required>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>
        <div class="dash-hero">
            <div>
                <div class="dash-hero-kicker"><i class="fas fa-landmark"></i> Dashboard Admin</div>
                <h2>Pemerintah Kabupaten Donggala</h2>
                <p>Sistem Informasi Manajemen Label Digital (eLabel) - BPKAD Bidang Aset</p>
            </div>
            <span class="dash-chip"><i class="fas fa-shield-alt mr-1"></i> Sistem Resmi</span>
        </div>

        <div class="dash-grid">
            <div class="dash-card">
                <h3>Statistik</h3>
                <div class="chart-box">
                    <canvas id="dashboardChart" height="200"></canvas>
                </div>
                <div class="chart-legend">
                    <span><span class="legend-dot" style="background:#3b82f6;"></span>Data Ringkasan</span>
                </div>

                <div class="dash-mini">
                    <div class="mini-card">
                        <div class="mini-icon" style="background:#22c55e;"><i class="fas fa-box"></i></div>
                        <div class="mini-info">
                            <div class="mini-label">Jumlah Box</div>
                            <div class="mini-value"><?= esc((string) $boxCount) ?></div>
                        </div>
                    </div>
                    <div class="mini-card">
                        <div class="mini-icon" style="background:#f59e0b;"><i class="fas fa-folder-open"></i></div>
                        <div class="mini-info">
                            <div class="mini-label">Jumlah BPKB</div>
                            <div class="mini-value"><?= esc((string) $bpkbCount) ?></div>
                        </div>
                    </div>
                    <div class="mini-card">
                        <div class="mini-icon" style="background:#1d4ed8;"><i class="fas fa-file-signature"></i></div>
                        <div class="mini-info">
                            <div class="mini-label">Sertipikat Tanah</div>
                            <div class="mini-value"><?= esc((string) $sertifikatCount) ?></div>
                        </div>
                    </div>
                    <div class="mini-card">
                        <div class="mini-icon" style="background:#d8a928;"><i class="fas fa-file-contract"></i></div>
                        <div class="mini-info">
                            <div class="mini-label">Surat Penyerahan</div>
                            <div class="mini-value"><?= esc((string) $suratPenyerahanCount) ?></div>
                        </div>
                    </div>
                    <div class="mini-card">
                        <div class="mini-icon" style="background:#38bdf8;"><i class="fas fa-user-shield"></i></div>
                        <div class="mini-info">
                            <div class="mini-label">Role</div>
                            <div class="mini-value"><?= esc((string) $role) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dash-card">
                <h3>Ringkasan</h3>
                <div class="dash-statlist">
                    <div class="dash-stat">
                        <div class="label">Jumlah Box</div>
                        <div class="value"><?= esc((string) $boxCount) ?></div>
                    </div>
                    <div class="dash-stat">
                        <div class="label">Jumlah BPKB</div>
                        <div class="value"><?= esc((string) $bpkbCount) ?></div>
                    </div>
                    <div class="dash-stat">
                        <div class="label">Jumlah Sertipikat Tanah</div>
                        <div class="value"><?= esc((string) $sertifikatCount) ?></div>
                    </div>
                    <div class="dash-stat">
                        <div class="label">Jumlah Surat Penyerahan</div>
                        <div class="value"><?= esc((string) $suratPenyerahanCount) ?></div>
                    </div>
                    <div class="dash-stat">
                        <div class="label">Jumlah BPKB Keluar</div>
                        <div class="value"><?= esc((string) $bpkbDeletedCount) ?></div>
                    </div>
                    <div class="dash-stat">
                        <div class="label">Jumlah Peminjaman</div>
                        <div class="value"><?= esc((string) $loanCount) ?></div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="activity-toolbar">
                        <h3>Riwayat Aktifitas</h3>
                        <?php if (($role ?? '') === 'super_admin'): ?>
                            <form class="activity-cleanup" action="<?= site_url('admin/activity-logs/cleanup') ?>" method="post" onsubmit="return confirm('Bersihkan riwayat aktifitas yang lebih lama dari 180 hari?');">
                                <?= csrf_field() ?>
                                <button type="submit" aria-label="Bersihkan riwayat aktifitas lama" title="<?= esc((string) ($oldActivity180Count ?? 0)) ?> riwayat lebih lama dari 180 hari">
                                    <i class="fas fa-broom"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="activity-list">
                        <?php if (empty($activityLogs)): ?>
                            <div class="activity-empty">Belum ada riwayat aktifitas aplikasi.</div>
                        <?php else: ?>
                            <?php
                                $activityIcons = [
                                    'create' => 'fa-plus',
                                    'update' => 'fa-edit',
                                    'delete' => 'fa-trash',
                                    'permanent_delete' => 'fa-user-lock',
                                    'restore' => 'fa-undo',
                                    'import' => 'fa-file-import',
                                    'approve' => 'fa-check',
                                    'reject' => 'fa-times',
                                    'return' => 'fa-reply',
                                    'toggle' => 'fa-toggle-on',
                                ];
                            ?>
                            <?php foreach ($activityLogs as $log): ?>
                                <?php
                                    $action = (string) ($log['action'] ?? 'default');
                                    $icon = $activityIcons[$action] ?? 'fa-history';
                                    $createdAt = ! empty($log['created_at']) ? strtotime((string) $log['created_at']) : false;
                                ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?= esc($action) ?>">
                                        <i class="fas <?= esc($icon) ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-head">
                                            <div class="activity-user"><?= esc((string) ($log['user_name'] ?? 'User tidak aktif')) ?></div>
                                            <div class="activity-time"><?= $createdAt ? esc(date('d/m H:i', $createdAt)) : '-' ?></div>
                                        </div>
                                        <p class="activity-desc"><?= esc((string) ($log['description'] ?? '-')) ?></p>
                                        <div class="activity-meta">
                                            <span class="activity-pill"><?= esc((string) ($log['module'] ?? '-')) ?></span>
                                            <span class="activity-pill"><?= esc(str_replace('_', ' ', $action)) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('dashboardChart');
        if (!ctx || !window.Chart) {
            return;
        }
        var summaryData = [
            <?= (int) $boxCount ?>,
            <?= (int) $bpkbCount ?>,
            <?= (int) $sertifikatCount ?>,
            <?= (int) $suratPenyerahanCount ?>,
            <?= (int) $bpkbDeletedCount ?>,
            <?= (int) $loanCount ?>
        ];
        var valueLabelPlugin = {
            id: 'valueLabelPlugin',
            afterDatasetsDraw: function (chart) {
                var ctx = chart.ctx;
                ctx.save();
                ctx.font = '700 11px "Plus Jakarta Sans", system-ui, sans-serif';
                ctx.fillStyle = '#0f172a';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                chart.data.datasets.forEach(function (dataset, datasetIndex) {
                    var meta = chart.getDatasetMeta(datasetIndex);
                    meta.data.forEach(function (bar, index) {
                        var value = dataset.data[index];
                        ctx.fillText(Number(value).toLocaleString('id-ID'), bar.x, bar.y - 6);
                    });
                });

                ctx.restore();
            }
        };
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Box', 'BPKB', 'Sertipikat', 'Surat Penyerahan', 'BPKB Keluar', 'Peminjaman'],
                datasets: [
                    {
                        label: 'Jumlah Data',
                        data: summaryData,
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.65)',
                            'rgba(245, 158, 11, 0.65)',
                            'rgba(29, 78, 216, 0.65)',
                            'rgba(216, 169, 40, 0.7)',
                            'rgba(239, 68, 68, 0.62)',
                            'rgba(56, 189, 248, 0.65)'
                        ],
                        borderColor: [
                            '#22c55e',
                            '#f59e0b',
                            '#1d4ed8',
                            '#d8a928',
                            '#ef4444',
                            '#38bdf8'
                        ],
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 34
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#64748b' }
                    },
                    y: {
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                        ticks: { font: { size: 10 }, color: '#64748b', precision: 0 }
                    }
                }
            },
            plugins: [valueLabelPlugin]
        });
    });
</script>
<?= $this->endSection() ?>
