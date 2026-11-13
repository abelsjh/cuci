<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1">Daftar Transaksi Pencucian</h1>
        <p class="text-muted small mb-0">Kelola dan pantau seluruh transaksi cuci mobil & helm secara terpadu.</p>
    </div>
    <div>
        <a href="<?= site_url('transactions/new') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
        </a>
    </div>
</div>

<!-- Card Filter & Pencarian -->
<div class="card shadow-sm border mb-4">
    <div class="card-body p-3">
        <form action="<?= site_url('transactions') ?>" method="get" class="row g-2 align-items-end">
            <!-- Baris 1 -->
            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-semibold text-muted mb-1">Pencarian</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="No. nota, nama pelanggan, plat..." value="<?= esc($filters['q'] ?? '') ?>">
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label small fw-semibold text-muted mb-1">Status Pengerjaan</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="antri" <?= ($filters['status'] ?? '') === 'antri' ? 'selected' : '' ?>>Antri</option>
                    <option value="dikerjakan" <?= ($filters['status'] ?? '') === 'dikerjakan' ? 'selected' : '' ?>>Dikerjakan</option>
                    <option value="selesai" <?= ($filters['status'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="diambil" <?= ($filters['status'] ?? '') === 'diambil' ? 'selected' : '' ?>>Diambil</option>
                    <option value="dibatalkan" <?= ($filters['status'] ?? '') === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label small fw-semibold text-muted mb-1">Pembayaran</label>
                <select name="payment_status" class="form-select form-select-sm">
                    <option value="">Semua Pembayaran</option>
                    <option value="belum" <?= ($filters['payment_status'] ?? '') === 'belum' ? 'selected' : '' ?>>Belum Lunas</option>
                    <option value="lunas" <?= ($filters['payment_status'] ?? '') === 'lunas' ? 'selected' : '' ?>>Lunas</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label small fw-semibold text-muted mb-1">Kategori Layanan</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <option value="mobil" <?= ($filters['category'] ?? '') === 'mobil' ? 'selected' : '' ?>>Mobil</option>
                    <option value="helm" <?= ($filters['category'] ?? '') === 'helm' ? 'selected' : '' ?>>Helm</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label small fw-semibold text-muted mb-1">Metode Bayar</label>
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">Semua Metode</option>
                    <option value="tunai" <?= ($filters['payment_method'] ?? '') === 'tunai' ? 'selected' : '' ?>>Tunai</option>
                    <option value="transfer" <?= ($filters['payment_method'] ?? '') === 'transfer' ? 'selected' : '' ?>>Transfer</option>
                    <option value="qris" <?= ($filters['payment_method'] ?? '') === 'qris' ? 'selected' : '' ?>>QRIS</option>
                </select>
            </div>

            <!-- Baris 2 -->
            <div class="col-lg-3 col-md-3 col-6">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($filters['start_date'] ?? '') ?>">
            </div>

            <div class="col-lg-3 col-md-3 col-6">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($filters['end_date'] ?? '') ?>">
            </div>

            <?php if (session()->get('user_role') === 'admin'): ?>
                <div class="col-lg-3 col-md-3 col-12">
                    <label class="form-label small fw-semibold text-muted mb-1">Kasir / Operator</label>
                    <select name="created_by" class="form-select form-select-sm">
                        <option value="">Semua Kasir</option>
                        <?php foreach ($cashiers as $cs): ?>
                            <option value="<?= $cs['id'] ?>" <?= ($filters['created_by'] ?? '') == $cs['id'] ? 'selected' : '' ?>>
                                <?= esc($cs['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="<?= (session()->get('user_role') === 'admin') ? 'col-lg-3 col-md-3 col-12' : 'col-lg-6 col-md-6 col-12' ?> d-flex gap-2">
                <button type="submit" class="btn btn-secondary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Terapkan Filter
                </button>
                <a href="<?= site_url('transactions') ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Transaksi -->
<div class="card shadow-sm border">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 140px;">No. Nota</th>
                        <th style="width: 130px;">Waktu Input</th>
                        <th>Pelanggan</th>
                        <th>Plat / Item</th>
                        <th class="text-end" style="width: 110px;">Total Biaya</th>
                        <th class="text-center" style="width: 80px;">Bayar</th>
                        <th class="text-center" style="width: 80px;">Metode</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th style="width: 110px;">Kasir</th>
                        <th class="text-end pe-3" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada transaksi yang sesuai dengan filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td class="ps-3 fw-bold">
                                    <a href="<?= site_url('transactions/' . $tx['id']) ?>" class="text-decoration-none">
                                        <?= esc($tx['invoice_no']) ?>
                                    </a>
                                </td>
                                <td class="small text-muted"><?= format_datetime($tx['created_at'], 'd-m-Y H:i') ?></td>
                                <td class="fw-semibold"><?= esc($tx['customer_name']) ?></td>
                                <td>
                                    <?php if ($tx['item_note']): ?>
                                        <span class="text-dark small"><?= esc($tx['item_note']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-dark"><?= format_rupiah($tx['total']) ?></td>
                                <td class="text-center"><?= payment_badge($tx['payment_status']) ?></td>
                                <td class="text-center small"><?= payment_method_label($tx['payment_method']) ?></td>
                                <td class="text-center"><?= status_badge($tx['status']) ?></td>
                                <td class="small text-muted"><?= esc($tx['cashier_name'] ?? '-') ?></td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('transactions/' . $tx['id']) ?>" class="btn btn-outline-primary" title="Lihat Detail Transaksi">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                        <a href="<?= site_url('transactions/' . $tx['id'] . '/print') ?>" class="btn btn-outline-secondary" target="_blank" title="Cetak Nota">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="card-footer bg-white border-top d-flex justify-content-end py-2">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
