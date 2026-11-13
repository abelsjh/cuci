<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1">Audit Log Aktivitas</h1>
        <p class="text-muted small mb-0">Catatan riwayat tindakan penting untuk transparansi dan audit sistem.</p>
    </div>
</div>

<!-- Card Filter -->
<div class="card shadow-sm border mb-4">
    <div class="card-body p-3">
        <form action="<?= site_url('audit-logs') ?>" method="get" class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Pengguna</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Semua Pengguna</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= esc($u['name']) ?> (<?= esc($u['username']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Jenis Tindakan (Action)</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">Semua Tindakan</option>
                    <option value="UPDATE_PRICE" <?= ($filters['action'] ?? '') === 'UPDATE_PRICE' ? 'selected' : '' ?>>Ubah Harga Layanan</option>
                    <option value="CANCEL_TRANSACTION" <?= ($filters['action'] ?? '') === 'CANCEL_TRANSACTION' ? 'selected' : '' ?>>Pembatalan Transaksi</option>
                    <option value="REOPEN_TRANSACTION" <?= ($filters['action'] ?? '') === 'REOPEN_TRANSACTION' ? 'selected' : '' ?>>Buka Transaksi Final</option>
                    <option value="RESET_PASSWORD" <?= ($filters['action'] ?? '') === 'RESET_PASSWORD' ? 'selected' : '' ?>>Reset Password</option>
                    <option value="LOGIN_FAILED" <?= ($filters['action'] ?? '') === 'LOGIN_FAILED' ? 'selected' : '' ?>>Login Gagal</option>
                    <option value="CREATE" <?= ($filters['action'] ?? '') === 'CREATE' ? 'selected' : '' ?>>Tambah Data</option>
                    <option value="UPDATE" <?= ($filters['action'] ?? '') === 'UPDATE' ? 'selected' : '' ?>>Ubah Data</option>
                </select>
            </div>

            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($filters['start_date'] ?? '') ?>">
            </div>

            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($filters['end_date'] ?? '') ?>">
            </div>

            <div class="col-md-2 col-sm-12 d-flex gap-2">
                <button type="submit" class="btn btn-secondary btn-sm w-100">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="<?= site_url('audit-logs') ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Audit Log -->
<div class="card shadow-sm border">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 170px;">Waktu</th>
                        <th style="width: 150px;">Pengguna</th>
                        <th style="width: 150px;">Aksi</th>
                        <th style="width: 110px;">Entitas</th>
                        <th>Detail Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                                Belum ada catatan audit log.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="ps-3 small text-muted font-monospace"><?= format_datetime($log['created_at'], 'd/m/Y H:i:s') ?></td>
                                <td>
                                    <?php if ($log['user_name']): ?>
                                        <div class="fw-semibold small"><?= esc($log['user_name']) ?></div>
                                        <span class="text-muted small">@<?= esc($log['username']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small font-italic">Sistem / Tamu</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $actionBadge = match ($log['action']) {
                                        'UPDATE_PRICE'       => 'text-bg-warning',
                                        'CANCEL_TRANSACTION' => 'text-bg-danger',
                                        'LOGIN_FAILED'       => 'text-bg-danger',
                                        'REOPEN_TRANSACTION' => 'text-bg-warning',
                                        'CREATE'             => 'text-bg-primary',
                                        'UPDATE'             => 'text-bg-info',
                                        'RESET_PASSWORD'     => 'text-bg-dark',
                                        default              => 'text-bg-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $actionBadge ?> font-monospace"><?= esc($log['action']) ?></span>
                                </td>
                                <td>
                                    <span class="badge text-bg-light border text-uppercase small"><?= esc($log['entity']) ?></span>
                                </td>
                                <td class="small">
                                    <?= esc($log['detail']) ?>
                                    <?php if ($log['entity_id']): ?>
                                        <span class="text-muted ms-1">(ID: <?= $log['entity_id'] ?>)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager): ?>
        <div class="card-footer bg-white d-flex justify-content-end py-2">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>