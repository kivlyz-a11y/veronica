<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="py-5" style="background: #f1f5f9;">
    <div class="container py-3">
        <!-- Search Form Header -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold mb-2">
                    PELAYANAN PUBLIK
                </span>
                <h2 class="fw-bold text-dark mb-2">Cek Status Permohonan</h2>
                <p class="text-muted small mb-4">
                    Masukkan Nomor Registrasi dan Nomor WhatsApp yang Anda gunakan saat mendaftar untuk memantau status verifikasi dan link Zoom.
                </p>

                <!-- Search Card -->
                <div class="card card-custom p-4 bg-white text-start shadow-sm">
                    <form action="<?= site_url('cek-status') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">Nomor Registrasi <span class="text-danger">*</span></label>
                                <input type="text" name="registration_number" class="form-control" placeholder="Contoh: VER-20260829-0001" value="<?= esc($regNum ?? '') ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="Nomor WA saat mendaftar" value="<?= esc($phone ?? '') ?>" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-veronika-primary w-100 py-2">
                                    <i class="bi bi-search me-1"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Result Section -->
        <?php if ($searched): ?>
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <?php if ($application): ?>
                        <!-- Application Found -->
                        <div class="card card-custom p-4 p-md-5 bg-white border-0 shadow">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-4 mb-4 gap-3">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Nomor Registrasi</span>
                                    <h3 class="fw-bold text-success mb-0"><?= esc($application['registration_number']) ?></h3>
                                    <small class="text-muted">Kode Booking: <strong><?= esc($application['booking_code']) ?></strong></small>
                                </div>
                                <div>
                                    <?= status_badge($application['status']) ?>
                                </div>
                            </div>

                            <!-- Progress Timeline Indicator -->
                            <div class="mb-5">
                                <h6 class="fw-bold text-dark mb-4"><i class="bi bi-diagram-3-fill text-success me-2"></i> Progress Permohonan:</h6>
                                
                                <?php
                                $statusSteps = ['Menunggu Verifikasi', 'Sedang Diverifikasi', 'Disetujui', 'Sedang Berlangsung', 'Selesai'];
                                $currStatus = $application['status'];
                                $isCancelled = in_array($currStatus, ['Ditolak', 'Dibatalkan', 'Tidak Hadir']);
                                ?>

                                <?php if ($isCancelled): ?>
                                    <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3">
                                        <i class="bi bi-x-circle-fill fs-4"></i>
                                        <div>
                                            <strong>Status Permohonan: <?= esc($currStatus) ?></strong><br>
                                            <?= esc($application['verification_notes'] ?: 'Permohonan tidak dapat dilanjutkan.') ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="row text-center position-relative g-2">
                                        <?php
                                        $stepIndex = 0;
                                        if (in_array($currStatus, ['Sedang Diverifikasi', 'Perlu Perbaikan'])) $stepIndex = 1;
                                        if (in_array($currStatus, ['Disetujui', 'Terjadwal'])) $stepIndex = 2;
                                        if (in_array($currStatus, ['Sedang Berlangsung'])) $stepIndex = 3;
                                        if (in_array($currStatus, ['Selesai'])) $stepIndex = 4;
                                        ?>
                                        <?php foreach (['Diajukan', 'Verifikasi Berkas', 'Disetujui / Terjadwal', 'Pelayanan Zoom', 'Selesai'] as $idx => $stLabel): ?>
                                            <div class="col">
                                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2 fw-bold text-white <?= $idx <= $stepIndex ? 'bg-success' : 'bg-secondary opacity-50' ?>" style="width: 38px; height: 38px;">
                                                    <?php if ($idx < $stepIndex): ?>
                                                        <i class="bi bi-check-lg"></i>
                                                    <?php else: ?>
                                                        <?= $idx + 1 ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="small fw-semibold <?= $idx <= $stepIndex ? 'text-dark' : 'text-muted opacity-75' ?>" style="font-size: 0.75rem;">
                                                    <?= $stLabel ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Zoom Link Banner Box -->
                            <div class="p-4 rounded-4 mb-4 <?= !empty($application['zoom_url']) ? 'bg-success-subtle border border-success' : 'bg-light border' ?>">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle p-3 text-white <?= !empty($application['zoom_url']) ? 'bg-success' : 'bg-secondary' ?>">
                                        <i class="bi bi-camera-video-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fw-bold mb-1 <?= !empty($application['zoom_url']) ? 'text-success' : 'text-dark' ?>">
                                            Informasi Link Zoom Meeting
                                        </h5>
                                        
                                        <?php if (!empty($application['zoom_url'])): ?>
                                            <p class="small text-muted mb-3">
                                                Petugas telah memasukkan tautan Zoom untuk sesi konsultasi Anda. Silakan bergabung tepat waktu sesuai jadwal pelayanan.
                                            </p>
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <a href="<?= esc($application['zoom_url']) ?>" target="_blank" class="btn btn-success fw-bold px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                                                    <i class="bi bi-box-arrow-up-right"></i> Buka Link Zoom
                                                </a>
                                                <?php if (!empty($application['zoom_meeting_id'])): ?>
                                                    <span class="small text-dark"><strong>Meeting ID:</strong> <?= esc($application['zoom_meeting_id']) ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($application['zoom_password'])): ?>
                                                    <span class="small text-dark"><strong>Passcode:</strong> <?= esc($application['zoom_password']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($application['zoom_notes'])): ?>
                                                <div class="mt-2 small text-muted"><strong>Catatan Petugas:</strong> <?= esc($application['zoom_notes']) ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <p class="small text-muted mb-0">
                                                <i class="bi bi-info-circle text-warning me-1"></i> Link Zoom belum tersedia. Silakan menunggu petugas memasukkan link Zoom sebelum waktu pelayanan Anda tiba.
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Table (with masked sensitive info) -->
                            <h6 class="fw-bold text-secondary mb-3 small text-uppercase" style="letter-spacing: 0.5px;">Ringkasan Data Permohonan:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle small mb-4">
                                    <tbody>
                                        <tr>
                                            <td class="bg-light fw-bold" style="width: 30%;">Nama Pemohon</td>
                                            <td class="fw-semibold"><?= esc($application['applicant_name']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-light fw-bold">NIK (Disamarkan)</td>
                                            <td><?= mask_nik($application['applicant_nik']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-light fw-bold">Nomor WhatsApp</td>
                                            <td><?= mask_phone($application['applicant_phone']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-light fw-bold">Status Pemohon</td>
                                            <td><?= esc($application['applicant_role']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-light fw-bold">Jenis Layanan</td>
                                            <td class="fw-bold text-success"><?= esc($application['service_name']) ?> <?= $application['sub_service_name'] ? "({$application['sub_service_name']})" : '' ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-light fw-bold">Keperluan / Pokok Permohonan</td>
                                            <td><?= esc($application['subject']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-light fw-bold">Jadwal Konsultasi</td>
                                            <td class="fw-bold text-dark">
                                                <i class="bi bi-calendar-check text-success me-1"></i> <?= format_indo_date($application['schedule_date']) ?>
                                                <span class="ms-2 badge bg-dark"><?= substr($application['schedule_start_time'], 0, 5) ?> – <?= substr($application['schedule_end_time'], 0, 5) ?> WITA</span>
                                            </td>
                                        </tr>
                                        <?php if (!empty($application['verification_notes'])): ?>
                                        <tr>
                                            <td class="bg-light fw-bold text-warning-emphasis">Catatan Petugas</td>
                                            <td class="text-warning-emphasis fw-semibold"><?= esc($application['verification_notes']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center pt-2">
                                <a href="<?= site_url('unduh-bukti/' . $application['registration_number']) ?>" class="btn btn-outline-danger px-4 py-2 rounded-3 me-2">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF Bukti Pendaftaran
                                </a>
                                <a href="<?= site_url('/') ?>" class="btn btn-light px-4 py-2 rounded-3 border">
                                    <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Application Not Found -->
                        <div class="card card-custom p-5 text-center bg-white shadow-sm">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3 bg-danger-subtle text-danger" style="width: 64px; height: 64px; font-size: 1.8rem;">
                                <i class="bi bi-search"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Data Permohonan Tidak Ditemukan</h4>
                            <p class="text-muted small mb-4">
                                Pastikan Nomor Registrasi (contoh: <strong>VER-20260829-0001</strong>) dan Nomor WhatsApp yang Anda masukkan sudah benar dan sesuai saat pendaftaran.
                            </p>
                            <div>
                                <a href="<?= site_url('cek-status') ?>" class="btn btn-veronika-outline px-4 py-2">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Coba Cari Kembali
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
