<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard - SI VERONIKA PA Penajam') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --sidebar-bg: #072a19;
            --sidebar-hover: #0c4327;
            --sidebar-active: #0a5c36;
            --sidebar-width: 260px;
            --accent-gold: #d4af37;
            --topbar-height: 68px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1030;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .nav-category {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #64748b;
            padding: 18px 20px 6px;
        }

        .sidebar-menu .nav-link {
            color: #cbd5e1;
            padding: 11px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu .nav-link:hover {
            color: #ffffff;
            background: var(--sidebar-hover);
        }

        .sidebar-menu .nav-link.active {
            color: #ffffff;
            background: var(--sidebar-active);
            border-left-color: var(--accent-gold);
            font-weight: 600;
        }

        .sidebar-menu .nav-link i {
            font-size: 1.15rem;
        }

        /* Main Content Wrapper */
        .admin-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Topbar */
        .admin-topbar {
            height: var(--topbar-height);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1020;
            display: flex;
            align-items: center;
            padding: 0 28px;
        }

        .card-panel {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.03);
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .admin-wrapper {
                margin-left: 0;
            }
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1025;
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #0a5c36; color: #fff; font-size: 1.2rem; border: 2px solid var(--accent-gold);">
                <i class="bi bi-bank"></i>
            </div>
            <div>
                <div class="fw-bold text-white fs-6">SI VERONIKA</div>
                <div style="font-size: 0.72rem; color: #94a3b8;">PA Penajam Panel</div>
            </div>
        </div>

        <?php $uri = service('uri'); ?>
        <ul class="nav flex-column sidebar-menu py-2 list-unstyled">
            <li class="nav-category">Navigasi Utama</li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/dashboard*') ? 'active' : '' ?>" href="<?= site_url('admin/dashboard') ?>">
                    <i class="bi bi-grid-1x2-fill text-warning"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/applications*') ? 'active' : '' ?>" href="<?= site_url('admin/applications') ?>">
                    <i class="bi bi-inbox-fill text-info"></i> Permohonan Layanan
                </a>
            </li>

            <li class="nav-category">Pelayanan & Antrean</li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('officer/checkin*') ? 'active' : '' ?>" href="<?= site_url('officer/checkin') ?>">
                    <i class="bi bi-qr-code-scan text-success"></i> Check-In & Layanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/schedules*') ? 'active' : '' ?>" href="<?= site_url('admin/schedules') ?>">
                    <i class="bi bi-calendar3 text-primary"></i> Jadwal & Slot
                </a>
            </li>

            <li class="nav-category">Laporan & Audit</li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/reports*') ? 'active' : '' ?>" href="<?= site_url('admin/reports') ?>">
                    <i class="bi bi-file-earmark-bar-graph text-warning"></i> Laporan Pelayanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/audit-logs*') ? 'active' : '' ?>" href="<?= site_url('admin/audit-logs') ?>">
                    <i class="bi bi-journal-text text-secondary"></i> Log Aktivitas (Audit)
                </a>
            </li>

            <?php if (in_array(session()->get('user_role'), ['superadmin', 'admin'])): ?>
                <li class="nav-category">Master & Konfigurasi</li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('admin/services*') ? 'active' : '' ?>" href="<?= site_url('admin/services') ?>">
                        <i class="bi bi-collection text-danger"></i> Jenis Layanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('admin/users*') ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>">
                        <i class="bi bi-people-fill text-info"></i> Manajemen Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('admin/settings*') ? 'active' : '' ?>" href="<?= site_url('admin/settings') ?>">
                        <i class="bi bi-gear-fill text-success"></i> Pengaturan & WhatsApp
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-category">Sesi</li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= site_url('auth/logout') ?>">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </li>
        </ul>
    </aside>

    <!-- Content Wrapper -->
    <div class="admin-wrapper">
        <!-- Topbar -->
        <header class="admin-topbar d-flex justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none shadow-none" id="btnSidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="d-none d-sm-block">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-bank me-1"></i> Pengadilan Agama Penajam
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Timezone WITA Badge -->
                <span class="badge bg-light text-secondary border px-2 py-1 rounded-2 d-none d-md-inline" style="font-size: 0.75rem;">
                    <i class="bi bi-clock me-1 text-primary"></i> <?= date('H:i') ?> WITA
                </span>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.95rem;">
                            <?= strtoupper(substr(session()->get('user_name') ?? 'P', 0, 1)) ?>
                        </div>
                        <div class="d-none d-md-block text-start" style="line-height: 1.2;">
                            <div class="fw-semibold small"><?= esc(session()->get('user_name')) ?></div>
                            <small class="text-muted text-uppercase" style="font-size: 0.68rem;"><?= esc(session()->get('user_role')) ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                        <li><h6 class="dropdown-header"><?= esc(session()->get('user_email')) ?></h6></li>
                        <li><a class="dropdown-item" href="<?= site_url('/') ?>" target="_blank"><i class="bi bi-globe me-2 text-success"></i> Lihat Web Publik</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-3 p-md-4 flex-grow-1">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div><?= session()->getFlashdata('message') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><?= session()->getFlashdata('warning') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>

        <footer class="bg-white border-top py-3 px-4 text-center text-md-start small text-muted d-flex flex-column flex-md-row justify-content-between">
            <div>&copy; <?= date('Y') ?> <strong>SI VERONIKA</strong> - Pengadilan Agama Penajam</div>
            <div class="mt-1 mt-md-0">Sistem Verifikasi Online CEKAdministrasi</div>
        </footer>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnSidebarToggle')?.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('show');
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
