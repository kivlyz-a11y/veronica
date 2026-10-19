<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Jadwal & Slot Pelayanan</h3>
        <p class="text-muted small mb-0">Atur kuota slot harian, buat jadwal berulang hari kerja (Senin - Jumat), dan kalender hari libur.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-primary fw-semibold px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#recurringSlotModal">
            <i class="bi bi-arrow-repeat fs-5"></i>
            <span>Jadwal Berulang (Senin - Jumat)</span>
        </button>
        <button type="button" class="btn btn-success fw-semibold px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSlotModal">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah Slot Tunggal</span>
        </button>
        <button type="button" class="btn btn-outline-danger fw-semibold px-3 py-2 rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#holidayModal">
            <i class="bi bi-calendar-x"></i>
            <span>Kelola Hari Libur</span>
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Schedule Slot Table for selected date -->
    <div class="col-lg-8">
        <div class="card card-panel p-4 mb-4">
            <!-- Date Filter Picker & Navigation -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-3 gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-calendar-check text-success me-2"></i><?= format_indo_date($dateFilter) ?>
                    </h5>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                        <?= count($slots) ?> slot terdaftar pada tanggal ini
                    </span>
                </div>

                <div class="d-flex align-items-center gap-1">
                    <a href="<?= site_url('admin/schedules?date=' . $prevDate) ?>" class="btn btn-sm btn-outline-secondary px-2" title="Hari Sebelumnya (<?= $prevDate ?>)">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <a href="<?= site_url('admin/schedules?date=' . date('Y-m-d')) ?>" class="btn btn-sm <?= $dateFilter === date('Y-m-d') ? 'btn-success text-white fw-bold' : 'btn-outline-secondary' ?>">
                        Hari Ini
                    </a>
                    <a href="<?= site_url('admin/schedules?date=' . $nextDate) ?>" class="btn btn-sm btn-outline-secondary px-2" title="Hari Berikutnya (<?= $nextDate ?>)">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <form action="<?= site_url('admin/schedules') ?>" method="GET" class="d-inline ms-2">
                        <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($dateFilter) ?>" onchange="this.form.submit()" title="Pilih Tanggal Tertentu">
                    </form>
                </div>
            </div>

            <!-- Quick Date Strip (Upcoming dates with slots) -->
            <?php if (!empty($upcomingDates)): ?>
                <div class="mb-4 pb-2 border-bottom">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-muted">
                            <i class="bi bi-calendar3-range me-1 text-success"></i> Tanggal Pelayanan Terdekat:
                        </span>
                        <span class="badge bg-light text-secondary border small" style="font-size: 0.72rem;">
                            <?= count($upcomingDates) ?> Hari Terjadwal
                        </span>
                    </div>
                    <div class="d-flex gap-2 overflow-auto pb-1" style="white-space: nowrap;">
                        <?php foreach (array_slice($upcomingDates, 0, 10) as $ud): ?>
                            <?php $isSelected = ($ud['date'] === $dateFilter); ?>
                            <a href="<?= site_url('admin/schedules?date=' . $ud['date']) ?>" 
                               class="btn btn-xs rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 <?= $isSelected ? 'btn-success text-white fw-bold shadow-sm' : 'btn-outline-secondary' ?>">
                                <span><?= date('d M', strtotime($ud['date'])) ?></span>
                                <span class="badge rounded-pill <?= $isSelected ? 'bg-white text-success' : 'bg-secondary' ?>"><?= $ud['slot_count'] ?></span>
                            </a>
                        <?php endforeach; ?>
                        <?php if (count($upcomingDates) > 10): ?>
                            <span class="text-muted small align-self-center ps-1" style="font-size: 0.75rem;">+<?= count($upcomingDates) - 10 ?> hari lainnya di panel kanan &rarr;</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-3 text-secondary opacity-50">
                                        <i class="bi bi-calendar2-x fs-1"></i>
                                    </div>
                                    <p class="mb-2 fw-semibold">Belum ada slot pelayanan pada <?= format_indo_date($dateFilter) ?>.</p>
                                    <?php if (!empty($nearestDate)): ?>
                                        <div class="alert alert-info py-2 px-3 d-inline-block small mb-3">
                                            <i class="bi bi-info-circle me-1"></i> Jadwal terdekat berikutnya:
                                            <strong><?= format_indo_date($nearestDate['date']) ?></strong> (<?= $nearestDate['slot_count'] ?> slot aktif).
                                            <a href="<?= site_url('admin/schedules?date=' . $nearestDate['date']) ?>" class="btn btn-xs btn-primary ms-2 py-0 px-2">Buka Tanggal Ini</a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#recurringSlotModal">
                                            <i class="bi bi-arrow-repeat me-1"></i> Buat Berulang (Senin - Jumat)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addSlotModal">
                                            <i class="bi bi-plus-circle me-1"></i> Tambah 1 Slot
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($slots as $slot): ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-clock me-1 text-primary"></i>
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
                                                <button type="submit" class="btn btn-xs btn-outline-danger p-1 px-2" title="Hapus Slot">
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

    <!-- Right: Upcoming Scheduled Workdays & Holiday List -->
    <div class="col-lg-4">
        <!-- Upcoming Configured Dates Panel -->
        <div class="card card-panel p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-calendar3 text-success me-2"></i> Jadwal Aktif Sistem
                </h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle small">
                    <?= count($upcomingDates ?? []) ?> Hari
                </span>
            </div>
            <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                Daftar tanggal yang memiliki slot pelayanan. Klik tanggal untuk langsung memeriksa slotnya:
            </p>

            <div class="list-group list-group-flush border rounded-3 overflow-auto" style="max-height: 280px;">
                <?php if (empty($upcomingDates)): ?>
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-calendar-x fs-4 d-block mb-1 opacity-50"></i>
                        Belum ada jadwal yang dibuat untuk hari ini ke depan.
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingDates as $ud): ?>
                        <?php $isCurrent = ($ud['date'] === $dateFilter); ?>
                        <a href="<?= site_url('admin/schedules?date=' . $ud['date']) ?>" 
                           class="list-group-item list-group-item-action py-2 px-3 d-flex justify-content-between align-items-center <?= $isCurrent ? 'bg-success-subtle border-start border-4 border-success fw-bold' : '' ?>">
                            <div>
                                <div class="<?= $isCurrent ? 'text-success' : 'text-dark' ?>">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    <?= format_indo_date($ud['date']) ?>
                                </div>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <?= date('l', strtotime($ud['date'])) ?>
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge <?= $isCurrent ? 'bg-success text-white' : 'bg-light text-dark border' ?>">
                                    <?= $ud['slot_count'] ?> Slot
                                </span>
                                <?php if ($isCurrent): ?>
                                    <div class="text-success small" style="font-size: 0.68rem;">Sedang Dilihat</div>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card card-panel p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-x-fill text-danger me-2"></i> Hari Libur</h5>
                <button type="button" class="btn btn-sm btn-outline-danger p-1 px-2 rounded-2" data-bs-toggle="modal" data-bs-target="#holidayModal">
                    <i class="bi bi-plus-lg"></i> Tambah
                </button>
            </div>
            <p class="text-muted small mb-3">Jadwal berulang akan secara otomatis melewati tanggal-tanggal libur yang tercatat di bawah ini.</p>

            <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                <?php if (empty($holidays)): ?>
                    <li class="text-muted p-3 bg-light rounded text-center">Tidak ada data hari libur nasional.</li>
                <?php else: ?>
                    <?php foreach ($holidays as $h): ?>
                        <li class="p-2 rounded-2 bg-light border d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark d-block"><?= esc($h['name']) ?></strong>
                                <small class="text-danger"><i class="bi bi-calendar-event me-1"></i><?= format_indo_date($h['holiday_date']) ?></small>
                            </div>
                            <form action="<?= site_url('admin/schedules/holiday/' . $h['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Hapus hari libur ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-xs btn-outline-danger p-1" title="Hapus Hari Libur">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Quick Help Card -->
        <div class="card card-panel p-4 bg-primary-subtle border-primary-subtle text-dark">
            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-info-circle-fill me-1"></i> Tips Pengelolaan Jadwal</h6>
            <ul class="small mb-0 ps-3 text-secondary">
                <li class="mb-1">Gunakan tombol <strong>Jadwal Berulang (Senin - Jumat)</strong> untuk menyiapkan seluruh slot kerja dalam 1 bulan sekaligus.</li>
                <li class="mb-1">Hari libur nasional yang ditambahkan akan dilewati secara otomatis saat jadwal digenerate.</li>
                <li>Khusus hari Jumat, sesi siang setelah pukul 11.30 WITA otomatis dilewati untuk menghormati jam sholat Jumat.</li>
            </ul>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: JADWAL BERULANG (SENIN - JUMAT) -->
<!-- ========================================== -->
<div class="modal fade" id="recurringSlotModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/schedules/recurring') ?>" method="POST" id="formRecurringSchedule">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-repeat fs-4"></i> Buat Jadwal Berulang (Senin s.d. Jumat)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small p-4">
                    <!-- 1. Rentang Tanggal -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-6">1. Rentang Tanggal Pelayanan <span class="text-danger">*</span></label>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Tanggal Mulai (Dari):</label>
                                <input type="date" name="start_date" id="rec_start_date" class="form-control" value="<?= esc($dateFilter) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Tanggal Selesai (Sampai):</label>
                                <input type="date" name="end_date" id="rec_end_date" class="form-control" value="<?= date('Y-m-d', strtotime($dateFilter . ' +30 days')) ?>" required>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small">Pilihan Cepat:</span>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-1 px-2" onclick="setQuickRange(7)">+1 Minggu</button>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-1 px-2" onclick="setQuickRange(14)">+2 Minggu</button>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-1 px-2 active" id="btnQuickMonth" onclick="setQuickRange(30)">+1 Bulan (30 Hari)</button>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-1 px-2" onclick="setQuickRange(90)">+3 Bulan (90 Hari)</button>
                        </div>
                    </div>

                    <!-- 2. Pilihan Hari Dalam Seminggu -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark fs-6 mb-0">2. Pilihan Hari Berulang <span class="text-danger">*</span></label>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary active" id="btnDaysWorkday" onclick="selectWorkdaysOnly()">Senin - Jumat Saja</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="selectAllDays()">Pilih Semua</button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <label class="btn btn-sm btn-outline-success day-checkbox-btn active">
                                <input type="checkbox" name="days[]" value="1" class="day-cb d-none" checked onchange="toggleDayBtn(this)">
                                <i class="bi bi-check2 me-1"></i> Senin
                            </label>
                            <label class="btn btn-sm btn-outline-success day-checkbox-btn active">
                                <input type="checkbox" name="days[]" value="2" class="day-cb d-none" checked onchange="toggleDayBtn(this)">
                                <i class="bi bi-check2 me-1"></i> Selasa
                            </label>
                            <label class="btn btn-sm btn-outline-success day-checkbox-btn active">
                                <input type="checkbox" name="days[]" value="3" class="day-cb d-none" checked onchange="toggleDayBtn(this)">
                                <i class="bi bi-check2 me-1"></i> Rabu
                            </label>
                            <label class="btn btn-sm btn-outline-success day-checkbox-btn active">
                                <input type="checkbox" name="days[]" value="4" class="day-cb d-none" checked onchange="toggleDayBtn(this)">
                                <i class="bi bi-check2 me-1"></i> Kamis
                            </label>
                            <label class="btn btn-sm btn-outline-success day-checkbox-btn active">
                                <input type="checkbox" name="days[]" value="5" class="day-cb d-none" checked onchange="toggleDayBtn(this)">
                                <i class="bi bi-check2 me-1"></i> Jumat
                            </label>
                            <label class="btn btn-sm btn-outline-secondary day-checkbox-btn">
                                <input type="checkbox" name="days[]" value="6" class="day-cb d-none" onchange="toggleDayBtn(this)">
                                <i class="bi bi-circle me-1"></i> Sabtu
                            </label>
                            <label class="btn btn-sm btn-outline-secondary day-checkbox-btn">
                                <input type="checkbox" name="days[]" value="7" class="day-cb d-none" onchange="toggleDayBtn(this)">
                                <i class="bi bi-circle me-1"></i> Minggu
                            </label>
                        </div>
                    </div>

                    <!-- 3. Pilihan Jam Slot Layanan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-6 mb-2">3. Waktu & Jam Slot Pelayanan <span class="text-danger">*</span></label>
                        
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="slot_mode" id="modePreset" value="preset" checked onchange="toggleSlotMode()">
                                <label class="form-check-label fw-semibold text-dark" for="modePreset">
                                    Paket Jam Layanan PTSP Online (Direkomendasikan)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="slot_mode" id="modeCustom" value="custom" onchange="toggleSlotMode()">
                                <label class="form-check-label fw-semibold text-dark" for="modeCustom">
                                    Tentukan Jam Manual (1 Slot Khusus)
                                </label>
                            </div>
                        </div>

                        <!-- Panel Preset Slots -->
                        <div id="panelPresetSlots" class="p-3 bg-light border rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold text-dark small"><i class="bi bi-sun text-warning me-1"></i> Pilih Slot Waktu yang Diaktifkan:</span>
                                <div>
                                    <button type="button" class="btn btn-xs btn-link text-decoration-none p-0 me-2" onclick="toggleAllPresetSlots(true)">Centang Semua</button>
                                    <button type="button" class="btn btn-xs btn-link text-decoration-none text-danger p-0" onclick="toggleAllPresetSlots(false)">Hapus Semua</button>
                                </div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted fw-bold d-block mb-1">Sesi Pagi (Senin s.d. Jumat):</small>
                                <div class="row g-2">
                                    <?php 
                                    $pagiSlots = [
                                        '08:00 - 08:30', '08:30 - 09:00', '09:00 - 09:30',
                                        '09:30 - 10:00', '10:00 - 10:30', '10:30 - 11:00'
                                    ];
                                    foreach ($pagiSlots as $ps): 
                                        $val = str_replace(' ', '', $ps);
                                    ?>
                                        <div class="col-6 col-md-4">
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input preset-cb" type="checkbox" name="preset_slots[]" value="<?= $val ?>" id="slot_<?= $val ?>" checked>
                                                <label class="form-check-label font-monospace" for="slot_<?= $val ?>"><?= $ps ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <hr class="my-2">

                            <div>
                                <small class="text-muted fw-bold d-block mb-1">Sesi Siang (Senin s.d. Kamis):</small>
                                <div class="row g-2">
                                    <?php 
                                    $siangSlots = [
                                        '13:30 - 14:00', '14:00 - 14:30', '14:30 - 15:00'
                                    ];
                                    foreach ($siangSlots as $ss): 
                                        $val = str_replace(' ', '', $ss);
                                    ?>
                                        <div class="col-6 col-md-4">
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input preset-cb" type="checkbox" name="preset_slots[]" value="<?= $val ?>" id="slot_<?= $val ?>" checked>
                                                <label class="form-check-label font-monospace" for="slot_<?= $val ?>"><?= $ss ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Custom Slot -->
                        <div id="panelCustomSlot" class="p-3 bg-light border rounded-3 d-none">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Jam Mulai</label>
                                    <input type="time" name="start_time" class="form-control" value="09:00">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Jam Selesai</label>
                                    <input type="time" name="end_time" class="form-control" value="09:30">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Kuota, Petugas, Catatan -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark">Kapasitas (Kuota Pemohon) <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control" value="1" min="1" max="10" required>
                            <small class="text-muted">Kuota antrean per slot waktu.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark">Petugas Pelaksana</label>
                            <select name="assigned_officer_id" class="form-select">
                                <option value="">-- Bebas / Ditugaskan Nanti --</option>
                                <?php foreach ($officers as $off): ?>
                                    <option value="<?= $off['id'] ?>"><?= esc($off['name']) ?> (<?= esc($off['role']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark">Catatan Jadwal</label>
                            <input type="text" name="notes" class="form-control" value="Slot Rutin PTSP Online" placeholder="Keterangan slot">
                        </div>
                    </div>

                    <!-- 5. Opsi Otomasi & Proteksi -->
                    <div class="p-3 bg-white border border-success-subtle rounded-3">
                        <label class="fw-bold text-dark mb-2 d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check text-success"></i> Opsi Otomasi & Perlindungan Konflik:
                        </label>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="skip_holidays" id="chkSkipHolidays" value="1" checked>
                            <label class="form-check-label" for="chkSkipHolidays">
                                <strong>Lewati Hari Libur Nasional & Cuti Bersama</strong> yang terdaftar di sistem.
                            </label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="skip_friday_afternoon" id="chkSkipFriday" value="1" checked>
                            <label class="form-check-label" for="chkSkipFriday">
                                <strong>Khusus Hari Jumat, lewati sesi siang</strong> (setelah 11.30 WITA untuk sholat Jumat & jam dinas).
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="skip_existing" id="chkSkipExisting" value="1" checked>
                            <label class="form-check-label" for="chkSkipExisting">
                                <strong>Cegah Duplikasi Slot</strong> (Lewati jika slot pada jam tersebut sudah pernah dibuat sebelumnya).
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Generate Jadwal Berulang Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: TAMBAH SLOT TUNGGAL (1 HARI) -->
<!-- ========================================== -->
<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/schedules') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Slot Jadwal Pelayanan (1 Hari)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <!-- Banner Rekomendasi Jadwal Berulang -->
                    <div class="alert alert-primary d-flex align-items-center gap-2 py-2 mb-3">
                        <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                        <div>
                            Ingin membuat jadwal berulang otomatis setiap <strong>Senin - Jumat</strong>? Gunakan tombol <strong><a href="#" class="alert-link" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#recurringSlotModal">Jadwal Berulang</a></strong>.
                        </div>
                    </div>

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

<!-- ========================================== -->
<!-- MODAL: KELOLA HARI LIBUR -->
<!-- ========================================== -->
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

<script>
function setQuickRange(days) {
    const startInput = document.getElementById('rec_start_date');
    const endInput   = document.getElementById('rec_end_date');
    
    let base = new Date();
    if (startInput.value) {
        base = new Date(startInput.value);
    }
    
    const target = new Date(base.getTime());
    target.setDate(target.getDate() + days);
    
    endInput.value = target.toISOString().split('T')[0];

    // Highlight button
    document.querySelectorAll('.btn-xs[onclick^="setQuickRange"]').forEach(b => b.classList.remove('active'));
    if (window.event && window.event.target) {
        window.event.target.classList.add('active');
    }
}

function selectWorkdaysOnly() {
    document.querySelectorAll('.day-cb').forEach(cb => {
        const val = parseInt(cb.value);
        cb.checked = (val >= 1 && val <= 5);
        updateDayBtnStyle(cb);
    });
    document.getElementById('btnDaysWorkday').classList.add('active');
}

function selectAllDays() {
    document.querySelectorAll('.day-cb').forEach(cb => {
        cb.checked = true;
        updateDayBtnStyle(cb);
    });
    document.getElementById('btnDaysWorkday').classList.remove('active');
}

function toggleDayBtn(cb) {
    updateDayBtnStyle(cb);
}

function updateDayBtnStyle(cb) {
    const parent = cb.closest('.day-checkbox-btn');
    const icon = parent.querySelector('i');
    if (cb.checked) {
        parent.classList.add('active', 'btn-outline-success');
        parent.classList.remove('btn-outline-secondary');
        icon.className = 'bi bi-check2 me-1';
    } else {
        parent.classList.remove('active', 'btn-outline-success');
        parent.classList.add('btn-outline-secondary');
        icon.className = 'bi bi-circle me-1';
    }
}

function toggleSlotMode() {
    const isPreset = document.getElementById('modePreset').checked;
    document.getElementById('panelPresetSlots').classList.toggle('d-none', !isPreset);
    document.getElementById('panelCustomSlot').classList.toggle('d-none', isPreset);
}

function toggleAllPresetSlots(check) {
    document.querySelectorAll('.preset-cb').forEach(cb => cb.checked = check);
}
</script>

<?= $this->endSection() ?>
