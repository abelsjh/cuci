<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1">Dashboard Ringkasan</h1>
        <p class="text-muted small mb-0">Pantau transaksi, antrean pengerjaan, dan pendapatan usaha hari ini.</p>
    </div>
    <div>
        <a href="<?= site_url('transactions/new') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
        </a>
    </div>
</div>

<!-- 4 Kartu Statistik -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border border-start border-primary border-4 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Transaksi Hari Ini</span>
                    <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                        <i class="bi bi-receipt fs-5"></i>
                    </span>
                </div>
                <div class="h2 fw-bold text-dark mb-1"><?= number_format($todayTransactionsCount) ?></div>
                <div class="text-muted small">Total nota dibuat hari ini</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border border-start border-success border-4 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Pendapatan Hari Ini</span>
                    <span class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </span>
                </div>
                <div class="h2 fw-bold text-success mb-1"><?= format_rupiah($todayRevenue) ?></div>
                <div class="text-muted small">Dari transaksi berstatus diambil</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border border-start border-warning border-4 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Dalam Antrean / Proses</span>
                    <span class="p-2 bg-warning bg-opacity-10 text-warning rounded-circle">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </span>
                </div>
                <div class="h2 fw-bold text-warning mb-1">
                    <?= number_format($statusCounts['antri'] + $statusCounts['dikerjakan']) ?>
                </div>
                <div class="text-muted small">
                    <?= $statusCounts['antri'] ?> Antri &bull; <?= $statusCounts['dikerjakan'] ?> Dikerjakan
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border border-start border-info border-4 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Selesai (Siap Diambil)</span>
                    <span class="p-2 bg-info bg-opacity-10 text-info rounded-circle">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </span>
                </div>
                <div class="h2 fw-bold text-info mb-1"><?= number_format($statusCounts['selesai']) ?></div>
                <div class="text-muted small">Menunggu pembayaran & pengambilan</div>
            </div>
        </div>
    </div>
</div>

<!-- Status Pengerjaan & Pendapatan Kategori -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-semibold text-dark fs-6">Status Pengerjaan Seluruh Kendaraan & Helm</h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-2 text-center">
                    <div class="col-4 col-sm">
                        <div class="p-3 bg-light border rounded-3">
                            <div class="fs-4 fw-bold text-secondary"><?= $statusCounts['antri'] ?></div>
                            <span class="badge text-bg-secondary mt-1">Antri</span>
                        </div>
                    </div>
                    <div class="col-4 col-sm">
                        <div class="p-3 bg-light border rounded-3">
                            <div class="fs-4 fw-bold text-warning"><?= $statusCounts['dikerjakan'] ?></div>
                            <span class="badge text-bg-warning mt-1">Dikerjakan</span>
                        </div>
                    </div>
                    <div class="col-4 col-sm">
                        <div class="p-3 bg-light border rounded-3">
                            <div class="fs-4 fw-bold text-info"><?= $statusCounts['selesai'] ?></div>
                            <span class="badge text-bg-info mt-1">Selesai</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm">
                        <div class="p-3 bg-light border rounded-3">
                            <div class="fs-4 fw-bold text-success"><?= $statusCounts['diambil'] ?></div>
                            <span class="badge text-bg-success mt-1">Diambil</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm">
                        <div class="p-3 bg-light border rounded-3">
                            <div class="fs-4 fw-bold text-danger"><?= $statusCounts['dibatalkan'] ?></div>
                            <span class="badge text-bg-danger mt-1">Dibatalkan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-semibold text-dark fs-6">Pendapatan Berdasarkan Kategori</h5>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-around gap-2">
                <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-white">
                    <div>
                        <span class="badge text-bg-primary mb-1">Cuci Mobil</span>
                        <div class="fw-bold fs-5 text-dark"><?= format_rupiah($categoryRevenue['mobil']) ?></div>
                    </div>
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                        <i class="bi bi-car-front fs-3"></i>
                    </div>
                </div>
                <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-white">
                    <div>
                        <span class="badge text-bg-dark mb-1">Cuci Helm</span>
                        <div class="fw-bold fs-5 text-dark"><?= format_rupiah($categoryRevenue['helm']) ?></div>
                    </div>
                    <div class="p-2 bg-dark bg-opacity-10 text-dark rounded-circle">
                        <i class="bi bi-shield-shaded fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5 Transaksi Terbaru -->
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-semibold text-dark fs-6">Transaksi Terbaru</h5>
        <a href="<?= site_url('transactions') ?>" class="btn btn-sm btn-outline-primary">
            Lihat Semua Transaksi <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 140px;">No. Nota</th>
                        <th style="width: 130px;">Waktu</th>
                        <th>Pelanggan</th>
                        <th>Plat / Item</th>
                        <th class="text-end" style="width: 120px;">Total Biaya</th>
                        <th class="text-center" style="width: 90px;">Pembayaran</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-end pe-3" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentTransactions)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentTransactions as $tx): ?>
                            <tr>
                                <td class="ps-3 fw-bold">
                                    <a href="<?= site_url('transactions/' . $tx['id']) ?>" class="text-decoration-none">
                                        <?= esc($tx['invoice_no']) ?>
                                    </a>
                                </td>
                                <td class="small text-muted"><?= format_datetime($tx['created_at'], 'd-m-Y H:i') ?></td>
                                <td class="fw-semibold"><?= esc($tx['customer_name']) ?></td>
                                <td><?= esc($tx['item_note'] ?: '-') ?></td>
                                <td class="text-end fw-bold text-dark"><?= format_rupiah($tx['total']) ?></td>
                                <td class="text-center"><?= payment_badge($tx['payment_status']) ?></td>
                                <td class="text-center"><?= status_badge($tx['status']) ?></td>
                                <td class="text-end pe-3">
                                    <a href="<?= site_url('transactions/' . $tx['id']) ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
