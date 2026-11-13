<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Aplikasi Cuci Mobil & Helm') ?> - <?= esc(get_setting('business_name', 'AutoClean')) ?></title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bs-body-bg: #f8fafc;
            --bs-border-color: #e2e8f0;
        }

        body {
            background-color: var(--bs-body-bg);
            color: #1e293b;
            font-size: 0.925rem;
        }

        /* Card Enhancements */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.625rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Table Styling */
        .table {
            color: #334155;
            font-size: 0.885rem;
        }

        .table > thead > tr > th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.03em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .table > tbody > tr > td {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            border-color: #f1f5f9;
        }

        /* Badge Enhancements */
        .badge {
            font-weight: 500;
            letter-spacing: 0.02em;
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
        }

        /* Navbar Enhancements */
        .navbar {
            padding-top: 0.625rem;
            padding-bottom: 0.625rem;
        }

        .navbar-nav .nav-link {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            transition: background-color 0.15s ease-in-out;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.12);
        }

        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Print styling */
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?= $this->include('partials/navbar') ?>
    
    <main class="container-xl py-4 flex-grow-1">
        <?= $this->include('partials/flash') ?>
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="bg-white border-top py-3 text-center text-muted small mt-auto no-print">
        <div class="container-xl">
            <span>&copy; <?= date('Y') ?> <?= esc(get_setting('business_name', 'AutoClean Car & Helmet Wash')) ?>. All rights reserved.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
