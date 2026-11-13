<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1">Laporan Pendapatan & Layanan</h1>
        <p class="text-muted small mb-0">Rekapitulasi pendapatan dari transaksi berstatus diambil (lunas) dan statistik layanan.</p>
    </div>
    <div>
        <a href="<?= site_url('reports/export?' . http_build_query([
                        'start_date'     => $startDate,
                        'end_date'       => $endDate,
                        'category'       => $category,
                        'created_by'     => $cashierId,
                        'payment_method' => $method,
                    ])) ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor ke CSV / Excel
        </a>
    </div>
</div>

<!-- Card Filter Laporan -->
<div class="card shadow-sm border mb-4">
    <div class="card-body">
        <form action="<?= site_url('reports') ?>" method="get" class="row g-2 align-items-end">
            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($startDate) ?>">
            </div>

            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($endDate) ?>">
            </div>

            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Kategori</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <option value="mobil" <?= $category === 'mobil' ? 'selected' : '' ?>>Mobil</option>
                    <option value="helm" <?= $category === 'helm' ? 'selected' : '' ?>>Helm</option>
                </select>
            </div>

            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Metode Bayar</label>
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">Semua Metode</option>
                    <option value="tunai" <?= $method === 'tunai' ? 'selected' : '' ?>>Tunai</option>
                    <option value="transfer" <?= $method === 'transfer' ? 'selected' : '' ?>>Transfer</option>
                    <option value="qris" <?= $method === 'qris' ? 'selected' : '' ?>>QRIS</option>
                </select>
            </div>

            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-semibold text-muted">Kasir</label>
                <select name="created_by" class="form-select form-select-sm">
                    <option value="">Semua Kasir</option>
                    <?php foreach ($cashiers as $cs): ?>
                        <option value="<?= $cs['id'] ?>" <?= $cashierId == $cs['id'] ? 'selected' : '' ?>>
                            <?= esc($cs['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 col-sm-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="<?= site_url('reports') ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- 3 Kartu Statistik Ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border border-start border-success border-4 h-100">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Total Pendapatan Bersih</span>
                <div class="h2 fw-bold text-success mb-1"><?= format_rupiah($totalRevenue) ?></div>
                <div class="text-muted small">Periode <?= format_datetime($startDate, 'd M Y') ?> - <?= format_datetime($endDate, 'd M Y') ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border border-start border-primary border-4 h-100">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Transaksi Selesai & Diambil</span>
                <div class="display-6 fw-bold text-primary mb-1"><?= number_format($totalTransactions) ?></div>
                <div class="text-muted small">Nota pengerjaan lunas & selesai</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border border-start border-info border-4 h-100">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Rata-Rata per Transaksi</span>
                <div class="h2 fw-bold text-info mb-1"><?= format_rupiah($avgTransaction) ?></div>
                <div class="text-muted small">Nilai nota rata-rata pelanggan</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tabel Layanan Terlaris (FR-37) -->
    <div class="col-lg-5">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-trophy text-warning me-2"></i>Layanan Terlaris
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Layanan</th>
                                <th class="text-center" style="width: 70px;">Qty</th>
                                <th class="text-end pe-3">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bestSellers)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada data pengerjaan layanan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bestSellers as $bs): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold"><?= esc($bs['service_name']) ?></div>
                                            <span class="small"><?= category_badge($bs['category']) ?></span>
                                        </td>
                                        <td class="text-center fw-bold fs-6"><?= $bs['total_qty'] ?></td>
                                        <td class="text-end pe-3 fw-bold"><?= format_rupiah($bs['total_subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rincian Transaksi Pendapatan -->
    <div class="col-lg-7">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-receipt text-primary me-2"></i>Rincian Transaksi Pendapatan
                </h5>
                <span class="badge text-bg-light border text-muted"><?= count($transactions) ?> Transaksi</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3">No. Nota</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Metode</th>
                                <th class="text-end pe-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $tx): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold">
                                            <a href="<?= site_url('transactions/' . $tx['id']) ?>" class="text-decoration-none">
                                                <?= esc($tx['invoice_no']) ?>
                                            </a>
                                        </td>
                                        <td class="small text-muted"><?= format_datetime($tx['created_at']) ?></td>
                                        <td><?= esc($tx['customer_name']) ?></td>
                                        <td><span class="small badge text-bg-light border"><?= payment_method_label($tx['payment_method']) ?></span></td>
                                        <td class="text-end pe-3 fw-bold text-success"><?= format_rupiah($tx['total']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>