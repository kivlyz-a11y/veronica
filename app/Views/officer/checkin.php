<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Pelayanan & Check-In Online Hari Ini</h3>
        <p class="text-muted small mb-0">Kelola antrean sesi Zoom, scan QR tiket pendaftaran, dan catat kehadiran pemohon.</p>
    </div>
    <div>
        <button type="button" class="btn btn-success fw-bold px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
            <i class="bi bi-qr-code-scan fs-5"></i> Buka Scanner Kamera QR
        </button>
    </div>
</div>

<!-- Queue List Card -->
<div class="card card-panel p-4">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-people-fill text-success me-2"></i> Antrean Layanan Tanggal: <?= format_indo_date(date('Y-m-d')) ?>
        </h5>
        <span class="badge bg-primary px-3 py-2 rounded-pill"><?= count($todayQueue) ?> Pemohon Terdaftar Hari Ini</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-uppercase">
                <tr>
                    <th>Waktu Pelayanan</th>
                    <th>No. Registrasi</th>
                    <th>Nama Pemohon</th>
                    <th>Layanan</th>
                    <th>Status Layanan</th>
                    <th>Status Check-In</th>
                    <th class="text-end">Aksi Pelayanan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todayQueue)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-check fs-2 text-success d-block mb-2"></i>
                            Tidak ada jadwal antrean permohonan untuk hari ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($todayQueue as $item): ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <?= substr($item['start_time'], 0, 5) ?> - <?= substr($item['end_time'], 0, 5) ?> WITA
                            </td>
                            <td>
                                <a href="<?= site_url('admin/applications/' . $item['id']) ?>" class="fw-bold text-success text-decoration-none">
                                    <?= esc($item['registration_number']) ?>
                                </a>
                            </td>
                            <td>
                                <strong><?= esc($item['applicant_name']) ?></strong><br>
                                <small class="text-muted"><i class="bi bi-whatsapp text-success me-1"></i><?= esc($item['applicant_phone']) ?></small>
                            </td>
                            <td><?= esc($item['service_name']) ?></td>
                            <td><?= status_badge($item['status']) ?></td>
                            <td>
                                <?php if (!empty($item['check_in_at'])): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-all me-1"></i> Checked In (<?= date('H:i', strtotime($item['check_in_at'])) ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum Check-In</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <!-- 1. Check In -->
                                    <?php if (empty($item['check_in_at'])): ?>
                                        <form action="<?= site_url('officer/applications/' . $item['id'] . '/checkin') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-success" title="Check-In">
                                                <i class="bi bi-person-check-fill me-1"></i> Check-In
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- 2. Start Service -->
                                    <?php if (!empty($item['check_in_at']) && empty($item['service_started_at'])): ?>
                                        <form action="<?= site_url('officer/applications/' . $item['id'] . '/start') ?>" method="POST" class="d-inline ms-1">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-primary text-white" title="Mulai Pelayanan Zoom">
                                                <i class="bi bi-play-fill me-1"></i> Mulai Layanan
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- 3. Finish Service -->
                                    <?php if (!empty($item['service_started_at']) && empty($item['service_ended_at'])): ?>
                                        <form action="<?= site_url('officer/applications/' . $item['id'] . '/finish') ?>" method="POST" class="d-inline ms-1">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success text-white" title="Selesaikan Pelayanan">
                                                <i class="bi bi-patch-check-fill me-1"></i> Selesai
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- 4. Mark Absent -->
                                    <?php if ($item['status'] !== 'Selesai' && $item['status'] !== 'Tidak Hadir'): ?>
                                        <form action="<?= site_url('officer/applications/' . $item['id'] . '/absent') ?>" method="POST" class="d-inline ms-1" onsubmit="return confirm('Tandai pemohon ini Tidak Hadir?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Tandai Tidak Hadir">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Detail Link -->
                                    <a href="<?= site_url('admin/applications/' . $item['id']) ?>" class="btn btn-light border ms-1" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Camera QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-camera me-1"></i> Scan QR Code Tiket Pemohon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="stopQrScanner()"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-reader" style="width: 100%; border-radius: 12px; overflow: hidden;"></div>
                <div id="qr-reader-results" class="mt-3 small text-muted">Arahkan kamera ke QR Code pada bukti pendaftaran pemohon.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="stopQrScanner()">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- HTML5-QRCode Scanner Library CDN -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let html5QrCode = null;

document.getElementById('qrScannerModal').addEventListener('shown.bs.modal', function () {
    html5QrCode = new Html5Qrcode("qr-reader");
    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        document.getElementById('qr-reader-results').innerHTML = `<div class="text-success fw-bold">QR Terdeteksi: ${decodedText}</div>`;
        stopQrScanner();
        // If decodedText is a URL, redirect to it
        if (decodedText.startsWith('http')) {
            window.location.href = decodedText;
        } else {
            alert('Hasil Scan: ' + decodedText);
        }
    };
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
        .catch(err => {
            document.getElementById('qr-reader-results').innerHTML = `<div class="text-danger small">Tidak dapat mengakses kamera: ${err}</div>`;
        });
});

function stopQrScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
        }).catch(err => console.error(err));
    }
}
</script>
<?= $this->endSection() ?>
