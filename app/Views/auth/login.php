<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-11 col-sm-8 col-md-6 col-lg-4">
        <div class="card shadow border rounded-3">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-2">
                        <i class="bi bi-droplet-half fs-1"></i>
                    </div>
                    <h1 class="h4 fw-bold mb-1"><?= esc(get_setting('business_name', 'AutoClean')) ?></h1>
                    <p class="text-muted small">Sistem Kasir & Antrean Cuci Mobil & Helm</p>
                </div>

                <form action="<?= site_url('login') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" placeholder="Masukkan username" required autofocus autocomplete="username">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                    </button>
                </form>

                <div class="mt-4 pt-3 border-top text-center text-muted small">
                    <p class="mb-1"><strong>Akun Bawaan:</strong></p>
                    <p class="mb-0">Admin: <code>admin</code> / <code>admin123</code></p>
                    <p class="mb-0">Kasir: <code>kasir</code> / <code>kasir123</code></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>