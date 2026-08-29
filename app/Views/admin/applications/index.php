<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Daftar Permohonan Layanan</h3>
        <p class="text-muted small mb-0">Kelola verifikasi berkas, jadwal konsultasi, dan pengiriman link Zoom.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card card-panel p-3 mb-4">
    <form action="<?= site_url('admin/applications') ?>" method="GET">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Cari Kata Kunci</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="No. Reg / Nama / WA / Pokok" value="<?= esc($filters['search'] ?? '') ?>">
                </div>
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

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Jenis Layanan</label>
                <select name="service_id" class="form-select form-select-sm">
                    <option value="">-- Semua Layanan --</option>
                    <?php foreach ($services as $srv): ?>
                        <option value="<?= $srv['id'] ?>" <?= ($filters['service_id'] ?? '') == $srv['id'] ? 'selected' : '' ?>><?= esc($srv['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= esc($filters['date_from'] ?? '') ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= esc($filters['date_to'] ?? '') ?>">
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-sm btn-success w-100 fw-semibold">
                    Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card card-panel p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>No. Registrasi</th>
                    <th>Nama Pemohon</th>
                    <th>Jenis Layanan</th>
                    <th>Jadwal Pelayanan (WITA)</th>
                    <th>Status</th>
                    <th>Link Zoom</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                <?php if (empty($applications)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Tidak ada data permohonan yang sesuai dengan filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('admin/applications/' . $app['id']) ?>" class="fw-bold text-success text-decoration-none">
                                    <?= esc($app['registration_number']) ?>
                                </a>
                                <div class="text-muted" style="font-size: 0.72rem;"><?= date('d/m/Y H:i', strtotime($app['created_at'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= esc($app['applicant_name']) ?></div>
                                <small class="text-muted"><i class="bi bi-whatsapp text-success me-1"></i><?= esc($app['applicant_phone']) ?></small>
                            </td>
                            <td>
                                <div><?= esc($app['service_name']) ?></div>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;" title="<?= esc($app['subject']) ?>">
                                    <?= esc($app['subject']) ?>
                                </small>
                            </td>
                            <td>
                                <?php if (!empty($app['schedule_date'])): ?>
                                    <div class="fw-semibold"><?= format_indo_date($app['schedule_date']) ?></div>
                                    <small class="badge bg-light text-dark border"><?= substr($app['schedule_start_time'], 0, 5) ?> - <?= substr($app['schedule_end_time'], 0, 5) ?> WITA</small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= status_badge($app['status']) ?>
                            </td>
                            <td>
                                <?php if (!empty($app['zoom_url'])): ?>
                                    <a href="<?= esc($app['zoom_url']) ?>" target="_blank" class="badge bg-success text-white text-decoration-none px-2 py-1">
                                        <i class="bi bi-camera-video me-1"></i> Zoom Tersedia
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Belum Ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= site_url('admin/applications/' . $app['id']) ?>" class="btn btn-sm btn-outline-primary px-3 rounded-2">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>
