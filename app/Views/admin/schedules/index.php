<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Jadwal & Slot Pelayanan</h3>
        <p class="text-muted small mb-0">Atur kuota slot harian, buka/tutup antrean, dan kalender hari libur.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success fw-semibold px-3 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSlotModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Slot Jadwal
        </button>
        <button type="button" class="btn btn-outline-danger fw-semibold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#holidayModal">
            <i class="bi bi-calendar-x me-1"></i> Kelola Hari Libur
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Schedule Slot Table for selected date -->
    <div class="col-lg-8">
        <div class="card card-panel p-4 mb-4">
            <!-- Date Filter Picker -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-3 mb-4 gap-2">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-success me-2"></i> Slot Pada: <?= format_indo_date($dateFilter) ?></h5>
                <form action="<?= site_url('admin/schedules') ?>" method="GET" class="d-flex align-items-center gap-2">
                    <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($dateFilter) ?>" onchange="this.form.submit()">
                </form>
            </div>

            <!-- Slots Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu (WITA)</th>
                            <th>Kapasitas</th>
                            <th>Terisi</th>
                            <th>Petugas Pelaksana</th>
                            <th>Status Slot</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($slots)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada slot pelayanan pada tanggal ini. Silakan klik "Tambah Slot Jadwal".
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($slots as $slot): ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <?= substr($slot['start_time'], 0, 5) ?> - <?= substr($slot['end_time'], 0, 5) ?>
                                    </td>
                                    <td><?= $slot['capacity'] ?> Pemohon</td>
                                    <td>
                                        <span class="badge <?= $slot['booked'] >= $slot['capacity'] ? 'bg-danger' : 'bg-success' ?>">
                                            <?= $slot['booked'] ?> / <?= $slot['capacity'] ?>
                                        </span>
                                    </td>
                                    <td><?= esc($slot['officer_name'] ?: 'Petugas PTSP') ?></td>
                                    <td>
                                        <?php if ($slot['status'] === 'active'): ?>
                                            <span class="badge bg-success">Aktif (Terbuka)</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ditutup</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <form action="<?= site_url('admin/schedules/' . $slot['id'] . '/toggle') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-outline-warning p-1 px-2" title="Buka/Tutup Slot">
                                                <?= $slot['status'] === 'active' ? 'Tutup' : 'Buka' ?>
                                            </button>
                                        </form>

                                        <?php if ($slot['booked'] == 0): ?>
                                            <form action="<?= site_url('admin/schedules/' . $slot['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus slot ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-xs btn-outline-danger p-1 px-2">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Holiday List Card -->
    <div class="col-lg-4">
        <div class="card card-panel p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-x-fill text-danger me-2"></i> Daftar Hari Libur</h5>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                <?php if (empty($holidays)): ?>
                    <li class="text-muted">Tidak ada data hari libur.</li>
                <?php else: ?>
                    <?php foreach ($holidays as $h): ?>
                        <li class="p-2 rounded-2 bg-light border d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark d-block"><?= esc($h['name']) ?></strong>
                                <small class="text-danger"><i class="bi bi-calendar-event me-1"></i><?= format_indo_date($h['holiday_date']) ?></small>
                            </div>
                            <form action="<?= site_url('admin/schedules/holiday/' . $h['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Hapus hari libur ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-xs btn-outline-danger p-1">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Add Slot Modal -->
<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/schedules') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Slot Jadwal Pelayanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Pelayanan</label>
                        <input type="date" name="date" class="form-control" value="<?= esc($dateFilter) ?>" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Jam Mulai</label>
                            <input type="time" name="start_time" class="form-control" value="08:00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Jam Selesai</label>
                            <input type="time" name="end_time" class="form-control" value="08:30" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kapasitas (Kuota Pemohon)</label>
                        <input type="number" name="capacity" class="form-control" value="1" min="1" max="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Petugas Penanggung Jawab</label>
                        <select name="assigned_officer_id" class="form-select">
                            <option value="">-- Pilih Petugas --</option>
                            <?php foreach ($officers as $off): ?>
                                <option value="<?= $off['id'] ?>"><?= esc($off['name']) ?> (<?= esc($off['role']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Slot (Opsional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Slot Khusus PTSP Online">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Simpan Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Holiday Modal -->
<div class="modal fade" id="holidayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/schedules/holiday') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Hari Libur Nasional / Instansi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Libur</label>
                        <input type="date" name="holiday_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Hari Libur / Keterangan</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Cuti Bersama Idul Fitri" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-semibold">Tambah Hari Libur</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
