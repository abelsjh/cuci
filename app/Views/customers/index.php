<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1">Daftar Pelanggan</h1>
        <p class="text-muted small mb-0">Kelola master pelanggan dan riwayat pencucian kendaraan/helm.</p>
    </div>
    <div>
        <a href="<?= site_url('customers/new') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Pelanggan
        </a>
    </div>
</div>

<div class="card shadow-sm border mb-4">
    <div class="card-body p-3">
        <form action="<?= site_url('customers') ?>" method="get" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari nama atau no. telepon..." value="<?= esc($keyword ?? '') ?>">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
                <?php if (! empty($keyword)): ?>
                    <a href="<?= site_url('customers') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">No</th>
                        <th>Nama Pelanggan</th>
                        <th>No. Telepon / HP</th>
                        <th>Catatan Kendaraan / Helm</th>
                        <th>Terdaftar</th>
                        <th class="text-end pe-3" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data pelanggan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        foreach ($customers as $c): ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $no++ ?></td>
                                <td class="fw-semibold">
                                    <a href="<?= site_url('customers/' . $c['id']) ?>" class="text-decoration-none">
                                        <?= esc($c['name']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($c['phone']): ?>
                                        <i class="bi bi-telephone text-muted me-1"></i><?= esc($c['phone']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="small text-muted"><?= esc($c['notes'] ?: '-') ?></span></td>
                                <td class="small text-muted"><?= format_datetime($c['created_at'], 'd-m-Y') ?></td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('customers/' . $c['id']) ?>" class="btn btn-outline-info" title="Lihat Riwayat">
                                            <i class="bi bi-clock-history"></i>
                                        </a>
                                        <a href="<?= site_url('customers/' . $c['id'] . '/edit') ?>" class="btn btn-outline-secondary" title="Ubah">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= site_url('customers/' . $c['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager): ?>
        <div class="card-footer bg-white d-flex justify-content-end py-2">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>