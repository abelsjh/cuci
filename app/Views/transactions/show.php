<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 fw-bold mb-0"><?= esc($transaction['invoice_no']) ?></h1>
            <?= status_badge($transaction['status']) ?>
            <?= payment_badge($transaction['payment_status']) ?>
        </div>
        <p class="text-muted small mb-0">
            Dibuat pada <?= format_datetime($transaction['created_at']) ?> oleh <strong><?= esc($transaction['cashier_name'] ?? 'Kasir') ?></strong>
            <?php if ($transaction['print_count'] > 0): ?>
                &bull; <span class="badge text-bg-light border text-muted"><i class="bi bi-printer me-1"></i>Dicetak <?= $transaction['print_count'] ?>x</span>
            <?php endif; ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('transactions/' . $transaction['id'] . '/print') ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> Cetak Nota
        </a>
        <a href="<?= site_url('transactions') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Rincian Item & Riwayat Status -->
    <div class="col-lg-8">
        <!-- Card Informasi Pelanggan -->
        <div class="card shadow-sm border mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Pelanggan</span>
                        <div class="fw-bold fs-5 text-dark"><?= esc($transaction['customer_name']) ?></div>
                        <?php if ($transaction['customer_phone']): ?>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= esc($transaction['customer_phone']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Kendaraan / Helm</span>
                        <div class="fw-semibold text-dark"><?= esc($transaction['item_note'] ?: '-') ?></div>
                        <div class="small text-muted">Keterangan fisik item yang dikerjakan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Rincian Item Layanan -->
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-semibold">Rincian Layanan Cuci</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>Layanan</th>
                                <th>Kategori</th>
                                <th class="text-end">Tarif Satuan</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($items as $item): ?>
                                <tr>
                                    <td class="ps-3 text-muted"><?= $no++ ?></td>
                                    <td class="fw-semibold"><?= esc($item['service_name']) ?></td>
                                    <td><?= category_badge($item['category'] ?? 'mobil') ?></td>
                                    <td class="text-end"><?= format_rupiah($item['price']) ?></td>
                                    <td class="text-center"><?= $item['qty'] ?></td>
                                    <td class="text-end pe-3 fw-bold"><?= format_rupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-semibold">Subtotal Kotor:</td>
                                <td class="text-end pe-3 fw-bold"><?= format_rupiah($transaction['subtotal']) ?></td>
                            </tr>
                            <?php if ($transaction['discount'] > 0): ?>
                                <tr>
                                    <td colspan="5" class="text-end fw-semibold text-danger">Potongan Diskon:</td>
                                    <td class="text-end pe-3 fw-bold text-danger">- <?= format_rupiah($transaction['discount']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="5" class="text-end fs-5 fw-bold text-primary">Total Biaya:</td>
                                <td class="text-end pe-3 fs-5 fw-bold text-primary"><?= format_rupiah($transaction['total']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Riwayat Status (Log Pengerjaan) -->
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-semibold">Riwayat Perubahan Status Pengerjaan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Waktu</th>
                                <th>Status</th>
                                <th>Diubah Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">Belum ada log status.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="ps-3 small text-muted"><?= format_datetime($log['created_at']) ?></td>
                                        <td><?= status_badge($log['status']) ?></td>
                                        <td class="fw-semibold small"><?= esc($log['changer_name'] ?? 'Sistem') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Pembayaran & Aksi -->
    <div class="col-lg-4">
        <!-- Card Pembayaran -->
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">Status Pembayaran</h5>
                <?= payment_badge($transaction['payment_status']) ?>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Total yang Harus Dibayar:</div>
                    <div class="h3 fw-bold text-primary"><?= format_rupiah($transaction['total']) ?></div>
                </div>

                <?php if ($transaction['payment_status'] === 'lunas'): ?>
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <dl class="row mb-0 small">
                            <dt class="col-6 text-muted">Metode:</dt>
                            <dd class="col-6 fw-bold text-end"><?= payment_method_label($transaction['payment_method']) ?></dd>

                            <dt class="col-6 text-muted">Uang Diterima:</dt>
                            <dd class="col-6 fw-bold text-end"><?= format_rupiah($transaction['paid_amount']) ?></dd>

                            <?php if ($transaction['payment_method'] === 'tunai'): ?>
                                <dt class="col-6 text-muted">Kembalian:</dt>
                                <dd class="col-6 fw-bold text-success text-end"><?= format_rupiah($transaction['change_amount']) ?></dd>
                            <?php endif; ?>

                            <dt class="col-6 text-muted">Waktu Lunas:</dt>
                            <dd class="col-6 text-muted text-end"><?= format_datetime($transaction['paid_at']) ?></dd>
                        </dl>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning py-2 small mb-3">
                        <i class="bi bi-exclamation-circle me-1"></i> Transaksi ini belum dibayar.
                    </div>
                    <button type="button" class="btn btn-success w-100 fw-semibold py-2" data-bs-toggle="modal" data-bs-target="#modalPay">
                        <i class="bi bi-cash-stack me-1"></i> Terima Pembayaran
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card Alur Pengerjaan & Kontrol Status -->
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-semibold">Kontrol Status Pengerjaan</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Status Saat Ini:</span>
                    <div class="fs-5 fw-bold"><?= status_badge($transaction['status']) ?></div>
                </div>

                <?php if ($transaction['status'] === 'dibatalkan'): ?>
                    <div class="alert alert-danger mb-3 small">
                        <strong>Dibatalkan:</strong> <?= esc($transaction['cancel_reason'] ?: 'Tanpa keterangan') ?>
                    </div>
                <?php endif; ?>

                <!-- Tombol Transisi Status Cepat -->
                <div class="d-grid gap-2 mb-3">
                    <?php if ($transaction['status'] === 'antri'): ?>
                        <form action="<?= site_url('transactions/' . $transaction['id'] . '/status') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="dikerjakan">
                            <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold">
                                <i class="bi bi-play-fill me-1"></i> Mulai Kerjakan (Cuci)
                            </button>
                        </form>
                    <?php elseif ($transaction['status'] === 'dikerjakan'): ?>
                        <form action="<?= site_url('transactions/' . $transaction['id'] . '/status') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="selesai">
                            <button type="submit" class="btn btn-info w-100 py-2 fw-semibold text-white">
                                <i class="bi bi-check-circle-fill me-1"></i> Tandai Selesai Dicuci
                            </button>
                        </form>
                    <?php elseif ($transaction['status'] === 'selesai'): ?>
                        <?php if ($transaction['payment_status'] === 'lunas'): ?>
                            <form action="<?= site_url('transactions/' . $transaction['id'] . '/status') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="diambil">
                                <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                                    <i class="bi bi-bag-check-fill me-1"></i> Ambil & Selesaikan
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn btn-success w-100 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPay">
                                <i class="bi bi-cash-stack me-1"></i> Bayar Dahulu Sebelum Diambil
                            </button>
                            <div class="form-text text-danger small">
                                * Status <em>Diambil</em> hanya dapat dipilih setelah pembayaran LUNAS.
                            </div>
                        <?php endif; ?>
                    <?php elseif ($transaction['status'] === 'diambil'): ?>
                        <div class="alert alert-success py-2 text-center small mb-0">
                            <i class="bi bi-check-all me-1"></i> Transaksi telah final (Diambil).
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Opsi Admin: Ubah Bebas Status / Mundur / Reopen -->
                <?php if (session()->get('user_role') === 'admin'): ?>
                    <div class="border-top pt-3 mt-3">
                        <button class="btn btn-sm btn-outline-secondary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#adminStatusOverride">
                            <i class="bi bi-shield-lock me-1"></i> Opsi Status (Administrator)
                        </button>
                        <div class="collapse mt-2" id="adminStatusOverride">
                            <form action="<?= site_url('transactions/' . $transaction['id'] . '/status') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Paksa Ganti Status:</label>
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="antri" <?= $transaction['status'] === 'antri' ? 'selected' : '' ?>>Antri</option>
                                        <option value="dikerjakan" <?= $transaction['status'] === 'dikerjakan' ? 'selected' : '' ?>>Dikerjakan</option>
                                        <option value="selesai" <?= $transaction['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                        <option value="diambil" <?= $transaction['status'] === 'diambil' ? 'selected' : '' ?>>Diambil</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-warning w-100">Ubah Status</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tombol Pembatalan Transaksi -->
                <?php if (! in_array($transaction['status'], ['diambil', 'dibatalkan'], true) || session()->get('user_role') === 'admin'): ?>
                    <div class="border-top pt-3 mt-3">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalCancel">
                            <i class="bi bi-x-circle me-1"></i> Batalkan Transaksi Ini
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Terima Pembayaran -->
<div class="modal fade" id="modalPay" tabindex="-1" aria-labelledby="modalPayLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('transactions/' . $transaction['id'] . '/pay') ?>" method="post" id="formPayModal">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPayLabel">Terima Pembayaran - <?= esc($transaction['invoice_no']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 text-center mb-3">
                        <div class="text-muted small">Total Tagihan:</div>
                        <div class="h2 fw-bold text-primary mb-0"><?= format_rupiah($transaction['total']) ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="payment_method" id="modal_pay_method" class="form-select" required>
                            <option value="tunai" selected>Tunai (Cash)</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <div class="mb-3" id="modal_cash_group">
                        <label class="form-label fw-semibold">Uang Diterima <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="paid_amount" id="modal_paid_amount" class="form-control" value="<?= (int) $transaction['total'] ?>" min="0" step="500" required>
                        </div>
                        <div class="d-flex justify-content-between mt-2 p-2 bg-light rounded">
                            <span class="text-muted small">Kembalian:</span>
                            <span class="fw-bold text-success fs-6" id="modal_change_display">Rp 0</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Batalkan Transaksi -->
<div class="modal fade" id="modalCancel" tabindex="-1" aria-labelledby="modalCancelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('transactions/' . $transaction['id'] . '/cancel') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="modalCancelLabel">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Batalkan Transaksi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($transaction['payment_status'] === 'lunas'): ?>
                        <div class="alert alert-warning small">
                            <strong>Perhatian:</strong> Transaksi ini sudah berstatus <strong>LUNAS</strong>. Pembatalan hanya dapat dilakukan oleh Administrator dan pengembalian uang fisik dicatat manual di luar sistem.
                        </div>
                    <?php endif; ?>

                    <p class="mb-2">Apakah Anda yakin ingin membatalkan transaksi <strong><?= esc($transaction['invoice_no']) ?></strong>?</p>

                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label fw-semibold">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="3" placeholder="Misal: Pelanggan terburu-buru, salah input jenis kendaraan, dll." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalAmount = <?= (float) $transaction['total'] ?>;
        const payMethod = document.getElementById('modal_pay_method');
        const paidInput = document.getElementById('modal_paid_amount');
        const changeDisplay = document.getElementById('modal_change_display');

        function toRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function updatePayModal() {
            if (!payMethod || !paidInput) return;
            const method = payMethod.value;

            if (method === 'transfer' || method === 'qris') {
                paidInput.value = totalAmount;
                paidInput.readOnly = true;
                changeDisplay.textContent = toRupiah(0);
            } else {
                paidInput.readOnly = false;
                const paid = parseFloat(paidInput.value) || 0;
                const change = Math.max(0, paid - totalAmount);
                changeDisplay.textContent = toRupiah(change);
            }
        }

        if (payMethod && paidInput) {
            payMethod.addEventListener('change', updatePayModal);
            paidInput.addEventListener('input', updatePayModal);
            updatePayModal();
        }
    });
</script>
<?= $this->endSection() ?>