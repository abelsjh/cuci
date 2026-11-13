<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm no-print">
    <div class="container-fluid px-lg-4">
        <!-- Brand / Logo Usaha -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2 me-lg-4" href="<?= site_url('dashboard') ?>">
            <div class="p-1 bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="bi bi-droplet-half text-white"></i>
            </div>
            <span class="fs-6"><?= esc(get_setting('business_name', 'AutoClean Car & Helmet Wash')) ?></span>
        </a>

        <?php if (session()->get('isLoggedIn')): ?>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Menu Kiri -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('dashboard*') ? 'active fw-semibold' : '' ?>" href="<?= site_url('dashboard') ?>">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= url_is('transactions*') ? 'active fw-semibold' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-receipt me-1"></i> Transaksi
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li>
                                <a class="dropdown-item" href="<?= site_url('transactions/new') ?>">
                                    <i class="bi bi-plus-circle me-2 text-primary"></i> Transaksi Baru
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('transactions') ?>">
                                    <i class="bi bi-list-ul me-2 text-secondary"></i> Daftar Transaksi
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= url_is('customers*') ? 'active fw-semibold' : '' ?>" href="<?= site_url('customers') ?>">
                            <i class="bi bi-people me-1"></i> Pelanggan
                        </a>
                    </li>

                    <?php if (session()->get('user_role') === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= url_is('services*') ? 'active fw-semibold' : '' ?>" href="<?= site_url('services') ?>">
                                <i class="bi bi-tags me-1"></i> Layanan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= url_is('reports*') ? 'active fw-semibold' : '' ?>" href="<?= site_url('reports') ?>">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= (url_is('settings*') || url_is('users*') || url_is('audit-logs*')) ? 'active fw-semibold' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear me-1"></i> Kelola Sistem
                            </a>
                            <ul class="dropdown-menu shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="<?= site_url('settings') ?>">
                                        <i class="bi bi-sliders me-2 text-primary"></i> Identitas & Footer Nota
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= site_url('users') ?>">
                                        <i class="bi bi-person-badge me-2 text-info"></i> Pengguna & Hak Akses
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?= site_url('audit-logs') ?>">
                                        <i class="bi bi-journal-text me-2 text-secondary"></i> Audit Log Aktivitas
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- Menu Kanan: Profil & Keluar -->
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2 py-1 px-2 rounded" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: rgba(255, 255, 255, 0.15);">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="fw-semibold small"><?= esc(session()->get('user_name') ?? 'Pengguna') ?></span>
                            <span class="badge bg-white text-primary text-uppercase px-2 py-1" style="font-size: 0.7rem;">
                                <?= esc(session()->get('user_role') ?? 'kasir') ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2">
                            <li><h6 class="dropdown-header small">Masuk sebagai: <strong><?= esc(session()->get('user_login') ?? '') ?></strong></h6></li>
                            <?php if (session()->get('user_role') === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item small" href="<?= site_url('settings') ?>">
                                        <i class="bi bi-gear me-2 text-muted"></i>Pengaturan Usaha
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item small text-danger" href="<?= site_url('logout') ?>" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>
