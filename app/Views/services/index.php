<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1">Master Layanan Cuci</h1>
        <p class="text-muted small mb-0">Kelola daftar layanan cuci mobil dan helm beserta tarif biaya.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddService">
            <i class="bi bi-plus-circle me-1"></i> Tambah Layanan
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
                        <th>Kategori</th>
                        <th>Nama Layanan</th>
                        <th class="text-end">Tarif / Harga</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data layanan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        foreach ($services as $s): ?>
                            <tr class="<?= ! $s['is_active'] ? 'table-secondary opacity-75' : '' ?>">
                                <td class="ps-3 text-muted"><?= $no++ ?></td>
                                <td><?= category_badge($s['category']) ?></td>
                                <td class="fw-semibold"><?= esc($s['name']) ?></td>
                                <td class="text-end fw-bold"><?= format_rupiah($s['price']) ?></td>
                                <td class="text-center">
                                    <?php if ($s['is_active']): ?>
                                        <span class="badge text-bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" title="Ubah"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditService<?= $s['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="<?= site_url('services/' . $s['id'] . '/toggle') ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn <?= $s['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $s['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                <i class="bi <?= $s['is_active'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                            </button>
                                        </form>
                                        <form action="<?= site_url('services/' . $s['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit Service -->
                            <div class="modal fade" id="modalEditService<?= $s['id'] ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $s['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="<?= site_url('services/' . $s['id'] . '/update') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalEditLabel<?= $s['id'] ?>">Ubah Layanan: <?= esc($s['name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                                    <select name="category" class="form-select" required>
                                                        <option value="mobil" <?= $s['category'] === 'mobil' ? 'selected' : '' ?>>Mobil</option>
                                                        <option value="helm" <?= $s['category'] === 'helm' ? 'selected' : '' ?>>Helm</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Nama Layanan <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="<?= esc($s['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Tarif / Harga (Rp) <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" name="price" class="form-control" value="<?= (int) $s['price'] ?>" min="0" step="500" required>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActive<?= $s['id'] ?>" <?= $s['is_active'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="editActive<?= $s['id'] ?>">Status Layanan Aktif</label>
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
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Service -->
<div class="modal fade" id="modalAddService" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('services/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddLabel">Tambah Layanan Cuci Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="mobil">Mobil</option>
                            <option value="helm">Helm</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Layanan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Misal: Cuci Mobil Salju" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tarif / Harga (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price" class="form-control" placeholder="45000" min="0" step="500" required>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addActive" checked>
                        <label class="form-check-label" for="addActive">Status Layanan Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Layanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>