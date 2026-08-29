<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Laporan & Rekapitulasi Pelayanan</h3>
        <p class="text-muted small mb-0">Statistik dan data pelayanan SI VERONIKA Pengadilan Agama Penajam.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/reports/excel?' . http_build_query($filters)) ?>" class="btn btn-success fw-semibold px-3 py-2 rounded-3 shadow-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel (XLSX)
        </a>
        <a href="<?= site_url('admin/reports/pdf?' . http_build_query($filters)) ?>" class="btn btn-danger fw-semibold px-3 py-2 rounded-3 shadow-sm text-white">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card card-panel p-3 mb-4">
    <form action="<?= site_url('admin/reports') ?>" method="GET">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Dari Tanggal Jadwal</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= esc($filters['date_from'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Sampai Tanggal Jadwal</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= esc($filters['date_to'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Jenis Layanan</label>
                <select name="service_id" class="form-select form-select-sm">
                    <option value="">-- Semua Layanan --</option>
                    <?php foreach ($services as $srv): ?>
                        <option value="<?= $srv['id'] ?>" <?= ($filters['service_id'] ?? '') == $srv['id'] ? 'selected' : '' ?>><?= esc($srv['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <?php
                    $allStatuses = ['Menunggu Verifikasi', 'Sedang Diverifikasi', 'Disetujui', 'Perlu Perbaikan', 'Ditolak', 'Terjadwal', 'Sedang Berlangsung', 'Selesai', 'Dibatalkan', 'Tidak Hadir'];
                    foreach ($allStatuses as $st):
                    ?>
                        <option value="<?= $st ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">
                    Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Statistical Indicators -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-panel p-3 text-center border-bottom border-4 border-primary">
            <span class="small text-muted fw-semibold">Total Permohonan</span>
            <h3 class="fw-extrabold text-primary mb-0 mt-1"><?= $reportData['stats']['total'] ?></h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-panel p-3 text-center border-bottom border-4 border-success">
            <span class="small text-muted fw-semibold">Selesai Dilayani</span>
            <h3 class="fw-extrabold text-success mb-0 mt-1"><?= $reportData['stats']['selesai'] ?></h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-panel p-3 text-center border-bottom border-4 border-warning">
            <span class="small text-muted fw-semibold">Layanan Informasi</span>
            <h3 class="fw-extrabold text-warning mb-0 mt-1"><?= $reportData['stats']['layanan_informasi'] ?></h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-panel p-3 text-center border-bottom border-4 border-info">
            <span class="small text-muted fw-semibold">Layanan Pendaftaran</span>
            <h3 class="fw-extrabold text-info mb-0 mt-1"><?= $reportData['stats']['layanan_pendaftaran'] ?></h3>
        </div>
    </div>
</div>

<!-- Report Table Card -->
<div class="card card-panel p-4">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0 small">
            <thead class="table-light text-center text-uppercase">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>No. Registrasi</th>
                    <th>Nama Pemohon</th>
                    <th>NIK</th>
                    <th>No. WhatsApp</th>
                    <th>Jenis Layanan</th>
                    <th>Jadwal Pelayanan</th>
                    <th>Status</th>
                    <th>Verifikator</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reportData['rows'])): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            Tidak ada data pelayanan yang ditemukan untuk periode ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($reportData['rows'] as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('admin/applications/' . $row['id']) ?>" class="fw-bold text-success text-decoration-none">
                                    <?= esc($row['registration_number']) ?>
                                </a>
                            </td>
                            <td><?= esc($row['applicant_name']) ?></td>
                            <td><?= esc($row['applicant_nik']) ?></td>
                            <td><?= esc($row['applicant_phone']) ?></td>
                            <td><?= esc($row['service_name']) ?></td>
                            <td>
                                <?= $row['schedule_date'] ? date('d/m/Y', strtotime($row['schedule_date'])) : '-' ?>
                                (<?= substr($row['schedule_start_time'] ?? '', 0, 5) ?> WITA)
                            </td>
                            <td class="text-center"><?= status_badge($row['status']) ?></td>
                            <td><?= esc($row['verifier_name'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
