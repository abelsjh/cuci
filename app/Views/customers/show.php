<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1"><?= esc($customer['name']) ?></h1>
        <p class="text-muted small mb-0">Informasi profil pelanggan dan riwayat seluruh transaksi pencucian.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('customers/' . $customer['id'] . '/edit') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i> Ubah Profil
        </a>
        <a href="<?= site_url('transactions/new?customer_id=' . $customer['id']) ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Buat Transaksi Baru
        </a>
        <a href="<?= site_url('customers') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-semibold">Data Pelanggan</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted small">Nama</dt>
                    <dd class="col-sm-7 fw-bold"><?= esc($customer['name']) ?></dd>

                    <dt class="col-sm-5 text-muted small">No. Telepon</dt>
                    <dd class="col-sm-7"><?= esc($customer['phone'] ?: '-') ?></dd>

                    <dt class="col-sm-5 text-muted small">Catatan Item</dt>
                    <dd class="col-sm-7"><?= esc($customer['notes'] ?: '-') ?></dd>

                    <dt class="col-sm-5 text-muted small">Terdaftar</dt>
                    <dd class="col-sm-7 small"><?= format_datetime($customer['created_at']) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">Riwayat Transaksi Pelanggan (<?= count($transactions) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Nota</th>
                                <th>Tanggal</th>
                                <th>Plat / Item</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Pembayaran</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi untuk pelanggan ini.</td>
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
                                        <td><?= esc($tx['item_note'] ?: '-') ?></td>
                                        <td class="text-end fw-semibold"><?= format_rupiah($tx['total']) ?></td>
                                        <td class="text-center"><?= payment_badge($tx['payment_status']) ?></td>
                                        <td class="text-center"><?= status_badge($tx['status']) ?></td>
                                        <td class="text-end pe-3">
                                            <a href="<?= site_url('transactions/' . $tx['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Detail
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
    </div>
</div>
<?= $this->endSection() ?>