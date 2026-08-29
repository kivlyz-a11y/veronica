<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="py-5" style="background: #f8fafc;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Card -->
                <div class="card card-custom p-4 p-md-5 text-center border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3 text-white shadow" style="width: 76px; height: 76px; background: linear-gradient(135deg, #0a5c36, #15803d);">
                        <i class="bi bi-check-lg" style="font-size: 2.8rem;"></i>
                    </div>

                    <h2 class="fw-extrabold text-dark mb-2">PENDAFTARAN BERHASIL</h2>
                    <p class="text-muted lead fs-6 mb-4">
                        Terima kasih, permintaan layanan Anda pada <strong>Pengadilan Agama Penajam</strong> telah berhasil didaftarkan.
                    </p>

                    <!-- Registration Number Highlight Box -->
                    <div class="p-4 rounded-4 bg-light border mb-4 text-start position-relative">
                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <div class="small text-muted text-uppercase fw-semibold mb-1" style="letter-spacing: 0.5px;">Nomor Registrasi Anda</div>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <h3 class="fw-bold text-success mb-0" id="regNumberText"><?= esc($application['registration_number']) ?></h3>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1" onclick="copyRegistrationNumber()" title="Salin Nomor Registrasi">
                                        <i class="bi bi-copy"></i> Salin
                                    </button>
                                </div>

                                <div class="d-flex flex-column gap-2 small">
                                    <div class="d-flex justify-content-between border-bottom pb-1">
                                        <span class="text-muted">Layanan:</span>
                                        <span class="fw-semibold text-dark"><?= esc($application['service_name']) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between border-bottom pb-1">
                                        <span class="text-muted">Tanggal Konsultasi:</span>
                                        <span class="fw-semibold text-dark"><?= format_indo_date($application['schedule_date']) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between border-bottom pb-1">
                                        <span class="text-muted">Waktu:</span>
                                        <span class="fw-bold text-dark"><?= substr($application['schedule_start_time'], 0, 5) ?> – <?= substr($application['schedule_end_time'], 0, 5) ?> WITA</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-1">
                                        <span class="text-muted">Status:</span>
                                        <div><?= status_badge($application['status']) ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 text-center border-start-md">
                                <div class="p-2 bg-white rounded-3 d-inline-block border shadow-sm mb-2" style="max-width: 150px;">
                                    <div class="qr-svg-wrapper">
                                        <?= $qrCodeSvg ?>
                                    </div>
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">QR Code Verifikasi Resmi</div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Alert -->
                    <div class="alert alert-info border-0 rounded-3 text-start small d-flex gap-3 align-items-center mb-4">
                        <i class="bi bi-whatsapp fs-2 text-success"></i>
                        <div>
                            <strong>Pesan WhatsApp Konfirmasi Telah Dikirim</strong><br>
                            Petugas akan memverifikasi permohonan Anda. Link Zoom Meeting akan dikirimkan otomatis ke nomor WhatsApp <strong><?= esc($application['applicant_phone']) ?></strong> sebelum waktu layanan dimulai.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-dark px-3 py-2 fw-semibold rounded-3" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Cetak Bukti Pendaftaran
                        </button>
                        <a href="<?= site_url('unduh-bukti/' . $application['registration_number']) ?>" class="btn btn-danger px-3 py-2 fw-semibold rounded-3 text-white">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                        </a>
                        <a href="<?= site_url('cek-status?nomor_registrasi=' . urlencode($application['registration_number']) . '&whatsapp=' . urlencode($application['applicant_phone'])) ?>" class="btn btn-veronika-primary px-3 py-2 fw-semibold rounded-3">
                            <i class="bi bi-search me-1"></i> Cek Status
                        </a>
                        <a href="<?= site_url('/') ?>" class="btn btn-light px-3 py-2 fw-semibold rounded-3 border">
                            <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyRegistrationNumber() {
    const regNum = document.getElementById('regNumberText').innerText;
    navigator.clipboard.writeText(regNum).then(() => {
        alert('Nomor Registrasi ' + regNum + ' berhasil disalin ke clipboard!');
    });
}
</script>
<?= $this->endSection() ?>
