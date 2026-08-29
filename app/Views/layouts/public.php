<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'SI VERONIKA - Pengadilan Agama Penajam') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --pa-primary: #0a5c36;
            --pa-primary-dark: #074327;
            --pa-primary-light: #15803d;
            --pa-accent: #d4af37;
            --pa-accent-dark: #b89628;
            --pa-bg: #f8fafc;
            --pa-surface: #ffffff;
            --pa-text-dark: #0f172a;
            --pa-text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--pa-bg);
            color: var(--pa-text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Government Bar */
        .gov-topbar {
            background: linear-gradient(135deg, var(--pa-primary-dark) 0%, var(--pa-primary) 100%);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.85rem;
            padding: 6px 0;
            border-bottom: 2px solid var(--pa-accent);
        }

        /* Navbar */
        .navbar-veronika {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .navbar-brand .brand-badge {
            background: var(--pa-primary);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        .btn-veronika-primary {
            background: linear-gradient(135deg, var(--pa-primary) 0%, var(--pa-primary-light) 100%);
            color: #ffffff;
            border: none;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(10, 92, 54, 0.25);
            transition: all 0.25s ease;
        }

        .btn-veronika-primary:hover {
            background: linear-gradient(135deg, var(--pa-primary-dark) 0%, var(--pa-primary) 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(10, 92, 54, 0.35);
        }

        .btn-veronika-outline {
            border: 2px solid var(--pa-primary);
            color: var(--pa-primary);
            font-weight: 600;
            padding: 9px 22px;
            border-radius: 10px;
            transition: all 0.25s ease;
        }

        .btn-veronika-outline:hover {
            background: var(--pa-primary);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.08);
        }

        /* Footer */
        footer {
            background: #0f172a;
            color: #94a3b8;
            margin-top: auto;
            border-top: 4px solid var(--pa-primary);
        }

        footer a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s;
        }

        footer a:hover {
            color: var(--pa-accent);
        }

        /* Toast & Alerts */
        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1080;
            min-width: 320px;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Top Gov Bar -->
    <div class="gov-topbar">
        <div class="container d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <i class="bi bi-shield-check text-warning me-1"></i>
                <span>Sistem Resmi Pelayanan Online <strong>Pengadilan Agama Penajam</strong></span>
            </div>
            <div class="d-none d-md-block">
                <i class="bi bi-clock me-1"></i>
                <span>Zona Waktu: <strong>WITA (Asia/Makassar)</strong></span>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <nav class="navbar navbar-expand-lg navbar-veronika sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('/') ?>">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0a5c36, #15803d); color: #fff; font-size: 1.4rem; font-weight: 800; border: 2px solid #d4af37;">
                    <i class="bi bi-bank"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark fs-5 tracking-tight">SI VERONIKA</span>
                        <span class="brand-badge">PA PENAJAM</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1;">Sistem Verifikasi Online CEKAdministrasi</div>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2 my-2 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark px-3" href="<?= site_url('/') ?>">
                            <i class="bi bi-house-door me-1 text-success"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark px-3" href="<?= site_url('ajukan-permintaan') ?>">
                            <i class="bi bi-send-plus me-1 text-success"></i> Ajukan Layanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark px-3" href="<?= site_url('cek-status') ?>">
                            <i class="bi bi-search me-1 text-success"></i> Cek Status
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <?php if (session()->get('is_logged_in')): ?>
                            <a class="btn btn-veronika-outline d-flex align-items-center gap-1" href="<?= site_url('admin/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard Petugas
                            </a>
                        <?php else: ?>
                            <a class="btn btn-sm btn-outline-secondary px-3 py-2 rounded-3" href="<?= site_url('auth/login') ?>">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login Petugas
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Alerts -->
    <div class="container mt-3">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div><?= session()->getFlashdata('message') ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?= session()->getFlashdata('error') ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                    <strong>Mohon perbaiki kesalahan berikut:</strong>
                </div>
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #0a5c36; color: #fff; font-size: 1.2rem; border: 2px solid #d4af37;">
                            <i class="bi bi-bank"></i>
                        </div>
                        <div>
                            <h5 class="text-white mb-0 fw-bold">SI VERONIKA</h5>
                            <small class="text-muted">Pengadilan Agama Penajam</small>
                        </div>
                    </div>
                    <p class="small text-secondary pe-lg-4">
                        Sistem Verifikasi Online CEKAdministrasi dan penjadwalan layanan konsultasi online melalui Zoom untuk masyarakat pencari keadilan di wilayah Kabupaten Penajam Paser Utara.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Tautan Cepat</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="<?= site_url('ajukan-permintaan') ?>"><i class="bi bi-chevron-right text-success me-1"></i> Ajukan Permintaan</a></li>
                        <li><a href="<?= site_url('cek-status') ?>"><i class="bi bi-chevron-right text-success me-1"></i> Cek Status Permohonan</a></li>
                        <li><a href="https://pa-penajam.go.id" target="_blank"><i class="bi bi-chevron-right text-success me-1"></i> Website Resmi PA Penajam</a></li>
                        <li><a href="<?= site_url('auth/login') ?>"><i class="bi bi-chevron-right text-success me-1"></i> Portal Petugas</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Kontak Resmi</h6>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2">
                        <li class="d-flex gap-2">
                            <i class="bi bi-geo-alt text-warning mt-1"></i>
                            <span><?= esc(get_setting('institution_address', 'Jl. Provinsi Km. 09, Nipah-Nipah, Penajam')) ?></span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-telephone text-warning"></i>
                            <span><?= esc(get_setting('institution_phone', '(0542) 7212345')) ?></span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-envelope text-warning"></i>
                            <span><?= esc(get_setting('institution_email', 'pa.penajam@gmail.com')) ?></span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-clock text-warning"></i>
                            <span><?= esc(get_setting('service_hours', 'Senin - Jumat: 08.00 - 15.30 WITA')) ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary my-4 opacity-25">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-secondary">
                <div>&copy; <?= date('Y') ?> <strong>Pengadilan Agama Penajam</strong>. Hak Cipta Dilindungi.</div>
                <div class="mt-2 mt-md-0">
                    Zona Waktu Resmi Pelayanan: <strong>WITA (Asia/Makassar)</strong>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
