<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1">Transaksi Pencucian Baru</h1>
        <p class="text-muted small mb-0">Input data pelanggan, pilih layanan mobil/helm, dan hitung biaya otomatis.</p>
    </div>
    <div>
        <a href="<?= site_url('transactions') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<form action="<?= site_url('transactions/create') ?>" method="post" id="formTransaction">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Kolom Form Input (Kiri) -->
        <div class="col-lg-8">
            <!-- Card Data Pelanggan & Item -->
            <div class="card shadow-sm border mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-person me-2 text-primary"></i>Data Pelanggan & Kendaraan / Helm
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customer_select" class="form-label fw-semibold">Pilih Pelanggan Terdaftar</label>
                            <select id="customer_select" name="customer_id" class="form-select">
                                <option value="">-- Pelanggan Umum / Walk-in --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['id'] ?>"
                                        data-name="<?= esc($c['name']) ?>"
                                        data-notes="<?= esc($c['notes']) ?>"
                                        <?= (old('customer_id') == $c['id'] || ($selectedCustomer && $selectedCustomer['id'] == $c['id'])) ? 'selected' : '' ?>>
                                        <?= esc($c['name']) ?> <?= $c['phone'] ? '(' . esc($c['phone']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Pilih jika pelanggan sudah tersimpan di database.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="customer_name" class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                value="<?= old('customer_name', $selectedCustomer['name'] ?? 'Umum') ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="item_note" class="form-label fw-semibold">Plat Nomor / Keterangan Item</label>
                            <input type="text" class="form-control" id="item_note" name="item_note"
                                value="<?= old('item_note', $selectedCustomer['notes'] ?? '') ?>"
                                placeholder="Contoh: B 1234 CD (Avanza Hitam) / Helm KYT NFR Merah">
                            <div class="form-text">Membantu mengenali fisik kendaraan atau helm saat pengerjaan.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Pemilihan Layanan Cuci (Dinamis) -->
            <div class="card shadow-sm border mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-droplet me-2 text-primary"></i>Pilihan Layanan Cuci
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddService">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Baris Layanan
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="tableServices">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 45%;">Layanan Cuci</th>
                                    <th class="text-end" style="width: 20%;">Tarif Satuan</th>
                                    <th class="text-center" style="width: 15%;">Qty</th>
                                    <th class="text-end" style="width: 20%;">Subtotal</th>
                                    <th class="text-center" style="width: 50px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="serviceRowsContainer">
                                <!-- Baris Pertama Default -->
                                <tr class="service-row">
                                    <td class="ps-3">
                                        <select name="service_ids[]" class="form-select service-select" required>
                                            <option value="">-- Pilih Layanan --</option>
                                            <optgroup label="Cuci Mobil">
                                                <?php foreach ($services as $s): if ($s['category'] !== 'mobil') continue; ?>
                                                    <option value="<?= $s['id'] ?>" data-price="<?= (int) $s['price'] ?>" data-category="mobil">
                                                        <?= esc($s['name']) ?> - <?= format_rupiah($s['price']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <optgroup label="Cuci Helm">
                                                <?php foreach ($services as $s): if ($s['category'] !== 'helm') continue; ?>
                                                    <option value="<?= $s['id'] ?>" data-price="<?= (int) $s['price'] ?>" data-category="helm">
                                                        <?= esc($s['name']) ?> - <?= format_rupiah($s['price']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                    </td>
                                    <td class="text-end fw-semibold text-muted item-unit-price">Rp 0</td>
                                    <td>
                                        <input type="number" name="quantities[]" class="form-control text-center item-qty" value="1" min="1" required>
                                    </td>
                                    <td class="text-end fw-bold item-subtotal">Rp 0</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Hapus Baris" disabled>
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card Opsi Pembayaran Langsung -->
            <div class="card shadow-sm border mb-4">
                <div class="card-header bg-white py-3">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_pay_now" name="is_pay_now" value="1">
                        <label class="form-check-label fw-semibold" for="is_pay_now">
                            <i class="bi bi-cash me-1 text-success"></i> Langsung Bayar Sekarang (Lunas di Muka)
                        </label>
                    </div>
                </div>
                <div class="card-body" id="paymentOptionsSection" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="payment_method" id="payment_method" class="form-select">
                                <option value="tunai" selected>Tunai (Cash)</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="paidAmountCol">
                            <label class="form-label fw-semibold">Nominal Uang Diterima <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="paid_amount" id="paid_amount" class="form-control" placeholder="0" min="0" step="500">
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted small">Kembalian:</span>
                                <span class="fw-bold text-success" id="displayChange">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Ringkasan Biaya (Kanan) -->
        <div class="col-lg-4">
            <div class="card shadow-sm border sticky-top" style="top: 1rem;">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-calculator me-2 text-primary"></i>Ringkasan Biaya
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal Kotor:</span>
                        <span class="fw-bold fs-6" id="displayGross">Rp 0</span>
                    </div>

                    <div class="mb-3 pt-2 border-top">
                        <label class="form-label small fw-semibold text-muted">Potongan Diskon (Opsional)</label>
                        <div class="input-group input-group-sm mb-1">
                            <select name="discount_type" id="discount_type" class="form-select" style="max-width: 90px;">
                                <option value="nominal">Rp</option>
                                <option value="percent">%</option>
                            </select>
                            <input type="number" name="discount_value" id="discount_value" class="form-control" value="0" min="0" step="1">
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Nilai Diskon:</span>
                            <span class="text-danger" id="displayDiscount">- Rp 0</span>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="text-muted small mb-1">Total yang Harus Dibayar:</div>
                        <div class="display-6 fw-bold text-primary" id="displayTotal">Rp 0</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm py-3 fw-bold" id="btnSubmit">
                        <i class="bi bi-check-circle me-1"></i> Simpan Transaksi
                    </button>
                    <div class="text-muted text-center small mt-2">
                        Status awal transaksi akan otomatis <strong>Antri</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Template Dropdown Layanan untuk JS baris baru -->
<template id="serviceOptionsTemplate">
    <option value="">-- Pilih Layanan --</option>
    <optgroup label="Cuci Mobil">
        <?php foreach ($services as $s): if ($s['category'] !== 'mobil') continue; ?>
            <option value="<?= $s['id'] ?>" data-price="<?= (int) $s['price'] ?>" data-category="mobil">
                <?= esc($s['name']) ?> - <?= format_rupiah($s['price']) ?>
            </option>
        <?php endforeach; ?>
    </optgroup>
    <optgroup label="Cuci Helm">
        <?php foreach ($services as $s): if ($s['category'] !== 'helm') continue; ?>
            <option value="<?= $s['id'] ?>" data-price="<?= (int) $s['price'] ?>" data-category="helm">
                <?= esc($s['name']) ?> - <?= format_rupiah($s['price']) ?>
            </option>
        <?php endforeach; ?>
    </optgroup>
</template>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('serviceRowsContainer');
        const btnAdd = document.getElementById('btnAddService');
        const optTemplate = document.getElementById('serviceOptionsTemplate').innerHTML;

        const displayGross = document.getElementById('displayGross');
        const discountType = document.getElementById('discount_type');
        const discountValue = document.getElementById('discount_value');
        const displayDiscount = document.getElementById('displayDiscount');
        const displayTotal = document.getElementById('displayTotal');

        const isPayNow = document.getElementById('is_pay_now');
        const paymentOptionsSection = document.getElementById('paymentOptionsSection');
        const paymentMethod = document.getElementById('payment_method');
        const paidAmount = document.getElementById('paid_amount');
        const displayChange = document.getElementById('displayChange');

        const customerSelect = document.getElementById('customer_select');
        const customerName = document.getElementById('customer_name');
        const itemNote = document.getElementById('item_note');

        // Sinkronisasi data pelanggan terdaftar
        customerSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (this.value) {
                customerName.value = selected.getAttribute('data-name') || '';
                const notes = selected.getAttribute('data-notes');
                if (notes && !itemNote.value) {
                    itemNote.value = notes;
                }
            }
        });

        // Format Rupiah helper
        function toRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        // Hitung Ulang Biaya Total
        function recalculate() {
            let gross = 0;
            const rows = container.querySelectorAll('.service-row');

            rows.forEach(row => {
                const select = row.querySelector('.service-select');
                const qtyInput = row.querySelector('.item-qty');
                const unitDisplay = row.querySelector('.item-unit-price');
                const subDisplay = row.querySelector('.item-subtotal');

                const opt = select.options[select.selectedIndex];
                const price = opt && opt.value ? parseFloat(opt.getAttribute('data-price')) : 0;
                const qty = Math.max(1, parseInt(qtyInput.value) || 1);

                const sub = price * qty;
                gross += sub;

                unitDisplay.textContent = toRupiah(price);
                subDisplay.textContent = toRupiah(sub);
            });

            displayGross.textContent = toRupiah(gross);

            // Diskon
            const dType = discountType.value;
            const dVal = parseFloat(discountValue.value) || 0;
            let discount = 0;

            if (dType === 'percent') {
                const cappedPercent = Math.min(100, Math.max(0, dVal));
                discount = (gross * cappedPercent) / 100;
            } else {
                discount = Math.min(gross, Math.max(0, dVal));
            }

            displayDiscount.textContent = '- ' + toRupiah(discount);

            const total = Math.max(0, gross - discount);
            displayTotal.textContent = toRupiah(total);

            // Update pembayaran jika aktif
            if (isPayNow.checked) {
                const method = paymentMethod.value;
                if (method === 'transfer' || method === 'qris') {
                    paidAmount.value = total;
                    paidAmount.readOnly = true;
                    displayChange.textContent = toRupiah(0);
                } else {
                    paidAmount.readOnly = false;
                    const paid = parseFloat(paidAmount.value) || 0;
                    const change = Math.max(0, paid - total);
                    displayChange.textContent = toRupiah(change);
                }
            }

            // Update tombol remove (disable jika tinggal 1)
            const allRemoveBtns = container.querySelectorAll('.btn-remove-row');
            allRemoveBtns.forEach(btn => btn.disabled = (allRemoveBtns.length <= 1));
        }

        // Tambah Baris Baru
        btnAdd.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.className = 'service-row';
            tr.innerHTML = `
            <td class="ps-3">
                <select name="service_ids[]" class="form-select service-select" required>
                    ${optTemplate}
                </select>
            </td>
            <td class="text-end fw-semibold text-muted item-unit-price">Rp 0</td>
            <td>
                <input type="number" name="quantities[]" class="form-control text-center item-qty" value="1" min="1" required>
            </td>
            <td class="text-end fw-bold item-subtotal">Rp 0</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Hapus Baris">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        `;
            container.appendChild(tr);
            recalculate();
        });

        // Event Delegation untuk baris layanan
        container.addEventListener('change', function(e) {
            if (e.target.matches('.service-select') || e.target.matches('.item-qty')) {
                recalculate();
            }
        });

        container.addEventListener('input', function(e) {
            if (e.target.matches('.item-qty')) {
                recalculate();
            }
        });

        container.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-remove-row');
            if (btn) {
                const rows = container.querySelectorAll('.service-row');
                if (rows.length > 1) {
                    btn.closest('.service-row').remove();
                    recalculate();
                }
            }
        });

        discountType.addEventListener('change', recalculate);
        discountValue.addEventListener('input', recalculate);

        // Opsi Pembayaran Langsung
        isPayNow.addEventListener('change', function() {
            if (this.checked) {
                paymentOptionsSection.style.display = 'block';
                paymentMethod.required = true;
                paidAmount.required = true;
            } else {
                paymentOptionsSection.style.display = 'none';
                paymentMethod.required = false;
                paidAmount.required = false;
            }
            recalculate();
        });

        paymentMethod.addEventListener('change', recalculate);
        paidAmount.addEventListener('input', recalculate);

        // Inisialisasi awal
        recalculate();
    });
</script>
<?= $this->endSection() ?>