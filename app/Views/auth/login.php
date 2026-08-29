<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login Petugas - SI VERONIKA') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #072a19 0%, #0a5c36 60%, #15803d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }

        .btn-login {
            background: linear-gradient(135deg, #0a5c36 0%, #15803d 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #074327 0%, #0a5c36 100%);
            color: #ffffff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="login-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: #0a5c36; color: #fff; font-size: 1.8rem; border: 3px solid #d4af37;">
                <i class="bi bi-bank"></i>
            </div>
            <h4 class="fw-extrabold text-dark mb-1">SI VERONIKA</h4>
            <div class="text-muted small">Portal Petugas Pengadilan Agama Penajam</div>
        </div>

        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show small rounded-3 border-0" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('message') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show small rounded-3 border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('auth/login') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold small text-secondary">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="admin@pa-penajam.go.id" value="<?= old('email') ?>" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small text-secondary">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Dashboard
            </button>

            <div class="text-center">
                <a href="<?= site_url('/') ?>" class="small text-muted text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
