<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 fw-bold mb-0">Ubah Data Pelanggan</h1>
            <a href="<?= site_url('customers') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border">
            <div class="card-body p-4">
                <form action="<?= site_url('customers/' . $customer['id'] . '/update') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $customer['name']) ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone', $customer['phone']) ?>">
                        <div class="form-text">Boleh dikosongkan. Jika diisi harus unik.</div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Catatan Kendaraan / Helm</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $customer['notes']) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Perbarui Pelanggan
                        </button>
                        <a href="<?= site_url('customers') ?>" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>