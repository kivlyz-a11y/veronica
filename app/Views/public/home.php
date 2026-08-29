<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #072a19 0%, #0a5c36 50%, #15803d 100%); color: #ffffff; position: relative; overflow: hidden;">
    <div class="position-absolute" style="top: -50px; right: -50px; width: 400px; height: 400px; background: radial-gradient(circle, rgba(212,175,55,0.15) 0%, rgba(255,255,255,0) 70%); border-radius: 50%;"></div>
    
    <div class="container py-4 position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(212, 175, 55, 0.4); font-size: 0.82rem;">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span>Pelayanan Terpadu Satu Pintu (PTSP) Online PA Penajam</span>
                </div>
                <h1 class="display-4 fw-extrabold mb-3" style="letter-spacing: -0.5px; line-height: 1.15;">
                    SI VERONIKA
                </h1>
                <h2 class="h4 fw-semibold text-warning mb-3">
                    Sistem Verifikasi Online CEKAdministrasi
                </h2>
                <p class="lead text-white-50 mb-4 pe-lg-4" style="font-size: 1.05rem;">
                    Pelayanan administrasi Pengadilan Agama Penajam secara mudah, cepat, transparan, dan terjadwal melalui konsultasi online via Zoom Meeting.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= site_url('ajukan-permintaan') ?>" class="btn btn-warning px-4 py-3 fw-bold rounded-3 text-dark shadow d-flex align-items-center gap-2">
                        <i class="bi bi-send-plus-fill fs-5"></i>
                        <span>AJUKAN PERMINTAAN</span>
                    </a>
                    <a href="<?= site_url('cek-status') ?>" class="btn btn-outline-light px-4 py-3 fw-semibold rounded-3 d-flex align-items-center gap-2">
                        <i class="bi bi-search fs-5"></i>
                        <span>CEK STATUS PERMOHONAN</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-custom p-4 bg-white text-dark">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-3 p-3 text-white" style="background: #0a5c36;">
                            <i class="bi bi-clock-history fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Jam Layanan Online</h5>
                            <small class="text-muted">Zona Waktu WITA</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                        <li class="d-flex justify-content-between p-2 rounded-2 bg-light">
                            <span class="fw-semibold"><i class="bi bi-calendar-check text-success me-1"></i> Senin – Kamis:</span>
                            <span class="text-dark fw-bold">08.00 – 15.30 WITA</span>
                        </li>
                        <li class="d-flex justify-content-between p-2 rounded-2 bg-light">
                            <span class="fw-semibold"><i class="bi bi-calendar-check text-success me-1"></i> Jumat:</span>
                            <span class="text-dark fw-bold">08.00 – 16.00 WITA</span>
                        </li>
                        <li class="d-flex justify-content-between p-2 rounded-2 bg-light">
                            <span class="fw-semibold text-danger"><i class="bi bi-x-circle text-danger me-1"></i> Sabtu & Minggu:</span>
                            <span class="text-danger fw-bold">TUTUP (Hari Libur)</span>
                        </li>
                    </ul>
                    <hr class="my-3">
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-info-circle text-success fs-5"></i>
                        <span>Link Zoom akan dimasukkan oleh petugas dan otomatis dikirimkan ke WhatsApp Anda.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Layanan Kami Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold mb-2">
                PILIHAN LAYANAN
            </span>
            <h2 class="fw-bold text-dark">Layanan Yang Tersedia</h2>
            <p class="text-muted">Pilih jenis layanan administrasi yang Anda butuhkan di Pengadilan Agama Penajam</p>
        </div>

        <div class="row g-4">
            <?php foreach ($services as $service): ?>
                <div class="col-lg-6">
                    <div class="card card-custom h-100 p-4 border-top border-4 border-success">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 p-3 text-white bg-success d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.5rem;">
                                <i class="bi <?= esc($service['icon'] ?? 'bi-info-circle-fill') ?>"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1"><?= esc($service['name']) ?></h4>
                                <p class="text-muted small mb-0"><?= esc($service['description']) ?></p>
                            </div>
                        </div>

                        <h6 class="fw-bold text-secondary mb-2 small text-uppercase" style="letter-spacing: 0.5px;">Cakupan Layanan:</h6>
                        <ul class="list-unstyled row g-2 mb-4">
                            <?php if (!empty($service['subcategories'])): ?>
                                <?php foreach ($service['subcategories'] as $sub): ?>
                                    <li class="col-sm-6 small d-flex align-items-start gap-2">
                                        <i class="bi bi-check2-circle text-success mt-1"></i>
                                        <span><?= esc($sub['name']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                        <div class="mt-auto pt-3 border-top">
                            <a href="<?= site_url('ajukan-permintaan?layanan=' . $service['slug']) ?>" class="btn btn-veronika-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                <span>Pilih <?= esc($service['name']) ?></span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Cara Menggunakan Section -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold mb-2">
                ALUR MUDAH & CEPAT
            </span>
            <h2 class="fw-bold text-dark">Cara Menggunakan SI VERONIKA</h2>
            <p class="text-muted">Ikuti 6 langkah mudah berikut untuk mendapatkan layanan online via Zoom</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php
            $steps = [
                ['num' => '1', 'title' => 'Pilih Layanan', 'desc' => 'Pilih Layanan Informasi atau Layanan Pendaftaran sesuai kebutuhan Anda.', 'icon' => 'bi-list-check'],
                ['num' => '2', 'title' => 'Isi Formulir', 'desc' => 'Lengkapi identitas diri, NIK, No. WhatsApp, dan unggah dokumen pendukung.', 'icon' => 'bi-pencil-square'],
                ['num' => '3', 'title' => 'Pilih Jadwal', 'desc' => 'Pilih tanggal dan slot waktu konsultasi online yang masih tersedia.', 'icon' => 'bi-calendar-event'],
                ['num' => '4', 'title' => 'Verifikasi Petugas', 'desc' => 'Petugas PTSP memeriksa kelengkapan administrasi yang diajukan.', 'icon' => 'bi-shield-check'],
                ['num' => '5', 'title' => 'Terima Link Zoom', 'desc' => 'Terima konfirmasi dan Link Zoom resmi melalui WhatsApp secara otomatis.', 'icon' => 'bi-whatsapp'],
                ['num' => '6', 'title' => 'Ikuti Layanan', 'desc' => 'Bergabung ke Zoom pada waktu jadwal, check-in, dan konsultasi dengan petugas.', 'icon' => 'bi-camera-video-fill'],
            ];
            ?>
            <?php foreach ($steps as $st): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom p-4 h-100 bg-white text-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3" style="width: 56px; height: 56px; background: linear-gradient(135deg, #0a5c36, #15803d); color: #fff; font-size: 1.4rem; font-weight: 800; border: 2px solid #d4af37;">
                            <?= $st['num'] ?>
                        </div>
                        <h5 class="fw-bold text-dark mb-2"><?= $st['title'] ?></h5>
                        <p class="text-muted small mb-0"><?= $st['desc'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= site_url('ajukan-permintaan') ?>" class="btn btn-veronika-primary btn-lg px-5 py-3 shadow">
                <i class="bi bi-send-plus-fill me-2"></i> Mulai Ajukan Permintaan Sekarang
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
