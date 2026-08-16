<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('title') ?>Log Integrasi API | ArsipKu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-network-wired text-primary mr-2"></i> Log Integrasi API (eLabel ↔ SIPAT)
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= site_url('admin/integration-logs') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh Log
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('partials/alerts') ?>

        <div class="card card-outline card-primary shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-history mr-1"></i> Riwayat Audit Sinkronisasi API
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table id="audit-log-table" class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th style="width: 140px;">Waktu</th>
                                <th style="width: 120px;">Event</th>
                                <th>NIBAR</th>
                                <th style="width: 90px;" class="text-center">Arah</th>
                                <th style="width: 100px;" class="text-center">Status</th>
                                <th>Operator</th>
                                <th style="width: 100px;" class="text-center">Aksi Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($logs as $row): 
                                $prettyChanges = json_encode(json_decode($row['changes'] ?? '{}'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            ?>
                                <tr>
                                    <td class="text-center text-muted font-weight-bold"><?= $i++ ?></td>
                                    <td class="small font-monospace"><?= esc($row['created_at'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge badge-secondary px-2 py-1 font-monospace">
                                            <?= esc($row['event_name'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="font-monospace text-primary font-weight-bold">
                                        <?= esc($row['nibar'] ?? '-') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (($row['direction'] ?? '') === 'outbound'): ?>
                                            <span class="badge badge-info"><i class="fas fa-arrow-up mr-1"></i> eLabel➔SIPAT</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fas fa-arrow-down mr-1"></i> SIPAT➔eLabel</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (($row['sync_status'] ?? '') === 'SUCCESS'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sukses</span>
                                        <?php elseif (($row['sync_status'] ?? '') === 'PENDING'): ?>
                                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-secondary"><?= esc($row['created_by'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-info btn-xs shadow-sm btn-detail-log"
                                                data-id="<?= $row['id'] ?>"
                                                data-eventid="<?= esc($row['event_id'] ?? '-') ?>"
                                                data-reason="<?= esc($row['reason'] ?? '-') ?>"
                                                data-changes="<?= esc($prettyChanges) ?>"
                                                data-error="<?= esc($row['error_message'] ?? '') ?>">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($queues)): ?>
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-tasks mr-1 text-warning"></i> Antrean Sinkronisasi Pending / Gagal (Retry Queue)
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive p-3">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Target URL</th>
                                    <th style="width: 90px;" class="text-center">Retry</th>
                                    <th style="width: 100px;" class="text-center">Status</th>
                                    <th>Pesan Error Terakhir</th>
                                    <th style="width: 120px;" class="text-center">Aksi Retry</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($queues as $q): ?>
                                    <tr>
                                        <td class="font-monospace small text-primary"><?= esc($q['target_url']) ?></td>
                                        <td class="text-center font-weight-bold"><?= $q['retry_count'] ?> / <?= $q['max_retries'] ?></td>
                                        <td class="text-center">
                                            <?php if ($q['status'] === 'DONE'): ?>
                                                <span class="badge badge-success">Selesai</span>
                                            <?php elseif ($q['status'] === 'PENDING'): ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Gagal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-danger font-monospace"><?= esc($q['last_error'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <?php if ($q['status'] !== 'DONE'): ?>
                                                <form action="<?= site_url('admin/integration-logs/retry/' . $q['id']) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-warning btn-xs shadow-sm" onclick="return confirm('Kirim ulang sinkronisasi ini?')">
                                                        <i class="fas fa-redo mr-1"></i> Sync Ulang
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small"><i class="fas fa-check"></i> Terproses</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Single Global Modal for Detail (Placed Outside Table to Prevent Freeze/Backdrop Bug) -->
<div class="modal fade" id="globalDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-info-circle mr-2"></i> Detail Log Sinkronisasi <span id="modal-log-id"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Event ID:</strong><br>
                        <span id="modal-event-id" class="font-monospace text-muted small"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Alasan / Trigger:</strong><br>
                        <span id="modal-reason" class="text-dark small"></span>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold">Data Perubahan (JSON Payload):</label>
                    <pre id="modal-changes" class="bg-light p-3 border rounded text-dark font-monospace small" style="max-height: 220px; overflow-y: auto;"></pre>
                </div>

                <div id="modal-error-container" class="alert alert-danger mb-0" style="display: none;">
                    <strong><i class="fas fa-exclamation-circle mr-1"></i> Pesan Kesalahan (Error Trace):</strong>
                    <pre id="modal-error-text" class="mb-0 text-white font-monospace small" style="white-space: pre-wrap;"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $('#audit-log-table').DataTable({
            pageLength: 15,
            order: [[0, 'asc']],
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Semua"]]
        });

        // Global Modal Event Handler (Prevents Backdrop Freezing & Formats JSON)
        $(document).on('click', '.btn-detail-log', function() {
            var btn = $(this);
            $('#modal-log-id').text('#' + btn.data('id'));
            $('#modal-event-id').text(btn.data('eventid'));
            $('#modal-reason').text(btn.data('reason'));

            var changes = btn.data('changes');
            if (typeof changes === 'object') {
                changes = JSON.stringify(changes, null, 2);
            }
            $('#modal-changes').text(changes || '{}');

            var err = btn.data('error');
            if (err && err.length > 0) {
                $('#modal-error-container').show();
                $('#modal-error-text').text(err);
            } else {
                $('#modal-error-container').hide();
                $('#modal-error-text').text('');
            }

            $('#globalDetailModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
