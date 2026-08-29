<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Dashboard Pelayanan Online</h3>
        <p class="text-muted small mb-0">Selamat datang di Panel Administrasi SI VERONIKA Pengadilan Agama Penajam.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('officer/checkin') ?>" class="btn btn-success fw-semibold px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-qr-code-scan"></i> Pelayanan & Check-In
        </a>
        <a href="<?= site_url('admin/applications') ?>" class="btn btn-outline-primary fw-semibold px-3 py-2 rounded-3">
            <i class="bi bi-inbox me-1"></i> Data Permohonan
        </a>
    </div>
</div>

<!-- Statistical Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card card-panel p-3 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Permintaan</span>
                    <h3 class="fw-extrabold text-dark mb-0 mt-1"><?= number_format($stats['total']) ?></h3>
                </div>
                <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                    <i class="bi bi-folder2-open fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card card-panel p-3 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Menunggu Verifikasi</span>
                    <h3 class="fw-extrabold text-warning mb-0 mt-1"><?= number_format($stats['menunggu_verifikasi']) ?></h3>
                </div>
                <div class="rounded-3 p-3 bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card card-panel p-3 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Jadwal Hari Ini</span>
                    <h3 class="fw-extrabold text-info mb-0 mt-1"><?= number_format($stats['hari_ini']) ?></h3>
                </div>
                <div class="rounded-3 p-3 bg-info-subtle text-info">
                    <i class="bi bi-calendar2-day fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card card-panel p-3 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pelayanan Selesai</span>
                    <h3 class="fw-extrabold text-success mb-0 mt-1"><?= number_format($stats['selesai']) ?></h3>
                </div>
                <div class="rounded-3 p-3 bg-success-subtle text-success">
                    <i class="bi bi-patch-check-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Trend Chart -->
    <div class="col-lg-8">
        <div class="card card-panel p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">Tren Permohonan 7 Hari Terakhir</h5>
                <span class="badge bg-light text-muted border">Permohonan Harian</span>
            </div>
            <div style="height: 260px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Breakdown Distribution Chart -->
    <div class="col-lg-4">
        <div class="card card-panel p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">Distribusi Jenis Layanan</h5>
            <div style="height: 260px; position: relative;">
                <canvas id="serviceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule Table -->
<div class="card card-panel p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-calendar-event text-primary me-2"></i> Jadwal Pelayanan Hari Ini (<?= format_indo_date(date('Y-m-d')) ?>)
            </h5>
            <small class="text-muted">Daftar antrean konsultasi online Zoom untuk hari ini</small>
        </div>
        <a href="<?= site_url('officer/checkin') ?>" class="btn btn-sm btn-outline-success">
            Buka Mode Check-In
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Waktu (WITA)</th>
                    <th>No. Registrasi</th>
                    <th>Nama Pemohon</th>
                    <th>Layanan</th>
                    <th>Status</th>
                    <th>Link Zoom</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                <?php if (empty($todaySchedules)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-4 d-block mb-1"></i>
                            Tidak ada jadwal konsultasi online untuk hari ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($todaySchedules as $sched): ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <?= substr($sched['start_time'], 0, 5) ?> - <?= substr($sched['end_time'], 0, 5) ?>
                            </td>
                            <td class="fw-semibold text-primary">
                                <a href="<?= site_url('admin/applications/' . $sched['id']) ?>" class="text-decoration-none">
                                    <?= esc($sched['registration_number']) ?>
                                </a>
                            </td>
                            <td>
                                <div><strong><?= esc($sched['applicant_name']) ?></strong></div>
                                <small class="text-muted"><?= esc($sched['applicant_phone']) ?></small>
                            </td>
                            <td><?= esc($sched['service_name']) ?></td>
                            <td><?= status_badge($sched['status']) ?></td>
                            <td>
                                <?php if (!empty($sched['zoom_url'])): ?>
                                    <a href="<?= esc($sched['zoom_url']) ?>" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1">
                                        <i class="bi bi-camera-video"></i> Buka Zoom
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Belum Ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= site_url('admin/applications/' . $sched['id']) ?>" class="btn btn-sm btn-light border" title="Buka Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: <?= $chartDates ?>,
            datasets: [{
                label: 'Permohonan Masuk',
                data: <?= $chartCounts ?>,
                borderColor: '#0a5c36',
                backgroundColor: 'rgba(10, 92, 54, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#d4af37'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // Service Breakdown Chart
    const serviceCtx = document.getElementById('serviceChart').getContext('2d');
    const serviceLabels = <?= json_encode(array_column($serviceDist, 'name')) ?>;
    const serviceData   = <?= json_encode(array_column($serviceDist, 'total')) ?>;

    new Chart(serviceCtx, {
        type: 'doughnut',
        data: {
            labels: serviceLabels,
            datasets: [{
                data: serviceData,
                backgroundColor: ['#0a5c36', '#d4af37', '#15803d', '#3b82f6', '#64748b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
