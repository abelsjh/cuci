<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1">Kelola Pengguna Sistem</h1>
        <p class="text-muted small mb-0">Atur akun akses untuk Administrator dan Kasir / Operator.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
        </button>
    </div>
</div>

<div class="card shadow-sm border">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Peran (Role)</th>
                        <th class="text-center">Status</th>
                        <th>Terdaftar</th>
                        <th class="text-end pe-3" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        foreach ($users as $u): ?>
                            <tr class="<?= ! $u['is_active'] ? 'table-secondary opacity-75' : '' ?>">
                                <td class="ps-3 text-muted"><?= $no++ ?></td>
                                <td class="fw-semibold">
                                    <?= esc($u['name']) ?>
                                    <?php if ((int) $u['id'] === (int) session()->get('user_id')): ?>
                                        <span class="badge text-bg-light border text-primary ms-1 small">Anda</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= esc($u['username']) ?></code></td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="badge text-bg-primary">Administrator</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-info">Kasir / Operator</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($u['is_active']): ?>
                                        <span class="badge text-bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= format_datetime($u['created_at']) ?></td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" title="Ubah Profil"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditUser<?= $u['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info" title="Reset Password"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalResetPass<?= $u['id'] ?>">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <?php if ((int) $u['id'] !== (int) session()->get('user_id')): ?>
                                            <form action="<?= site_url('users/' . $u['id'] . '/toggle') ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn <?= $u['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                    <i class="bi <?= $u['is_active'] ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit User -->
                            <div class="modal fade" id="modalEditUser<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="<?= site_url('users/' . $u['id'] . '/update') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Ubah Pengguna: <?= esc($u['name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="<?= esc($u['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                                                    <input type="text" name="username" class="form-control" value="<?= esc($u['username']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Peran (Role) <span class="text-danger">*</span></label>
                                                    <select name="role" class="form-select" required>
                                                        <option value="kasir" <?= $u['role'] === 'kasir' ? 'selected' : '' ?>>Kasir / Operator</option>
                                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                                                    </select>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editUserActive<?= $u['id'] ?>" <?= $u['is_active'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="editUserActive<?= $u['id'] ?>">Akun Aktif</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Reset Password -->
                            <div class="modal fade" id="modalResetPass<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="<?= site_url('users/' . $u['id'] . '/reset-password') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reset Password: <?= esc($u['name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info py-2 small mb-3">
                                                    Masukkan password baru untuk akun <strong><?= esc($u['username']) ?></strong>. Minimal 6 karakter.
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                                                    <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Reset Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add User -->
<div class="modal fade" id="modalAddUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('users/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Misal: Kasir Siang" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="kasir2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Peran (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="kasir" selected>Kasir / Operator</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addUserActive" checked>
                        <label class="form-check-label" for="addUserActive">Akun Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>