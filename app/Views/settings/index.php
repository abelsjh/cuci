<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Pengaturan Usaha & Nota</h1>
                <p class="text-muted small mb-0">Atur identitas bisnis cuci mobil & helm yang tampil pada nota cetak.</p>
            </div>
        </div>

        <div class="card shadow-sm border">
            <div class="card-body p-4">
                <form action="<?= site_url('settings') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="business_name" class="form-label fw-semibold">Nama Usaha / Bengkel Cuci <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="business_name" name="business_name" value="<?= old('business_name', $settings['business_name'] ?? '') ?>" required>
                        <div class="form-text">Nama yang tercetak di bagian paling atas nota transaksi.</div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Alamat Usaha</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?= old('address', $settings['address'] ?? '') ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone', $settings['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="invoice_prefix" class="form-label fw-semibold">Prefiks Nomor Nota <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" value="<?= old('invoice_prefix', $settings['invoice_prefix'] ?? 'CW') ?>" required maxlength="10">
                            <div class="form-text">Contoh: <code>CW</code> menghasilkan nomor nota <code>CW-<?= date('Ymd') ?>-0001</code>.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="receipt_footer" class="form-label fw-semibold">Teks Footer Nota</label>
                        <input type="text" class="form-control" id="receipt_footer" name="receipt_footer" value="<?= old('receipt_footer', $settings['receipt_footer'] ?? '') ?>">
                        <div class="form-text">Pesan terima kasih atau catatan garansi di bagian bawah struk.</div>
                    </div>

                    <div class="border-top pt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>