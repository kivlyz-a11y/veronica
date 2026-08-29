<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="py-5" style="background: #f1f5f9;">
    <div class="container">
        <!-- Breadcrumb & Header -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-success text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ajukan Permintaan</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1">Formulir Pengajuan Layanan Online</h2>
                <p class="text-muted small mb-0">Silakan lengkapi formulir pendaftaran konsultasi online via Zoom pada Pengadilan Agama Penajam.</p>
            </div>
        </div>

        <form action="<?= site_url('ajukan-permintaan') ?>" method="POST" enctype="multipart/form-data" id="applicationForm">
            <?= csrf_field() ?>

            <div class="row g-4">
                <!-- Left Column: Form Fields -->
                <div class="col-lg-8">
                    <!-- 1. DATA PEMOHON -->
                    <div class="card card-custom p-4 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <span class="badge bg-success rounded-circle p-2 px-3 fw-bold">1</span>
                            <h5 class="fw-bold mb-0 text-dark">Data Identitas Pemohon</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Sesuai KTP" value="<?= old('name') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                                <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" maxlength="16" value="<?= old('nik') ?>" required>
                                <small class="text-muted" style="font-size: 0.72rem;">NIK Anda dirahasiakan dan dilindungi.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="Contoh: 081234567890" value="<?= old('phone') ?>" required>
                                <small class="text-muted" style="font-size: 0.72rem;">Digunakan untuk menerima notifikasi & link Zoom.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Alamat Email (Opsional)</label>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="<?= old('email') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Status Pemohon <span class="text-danger">*</span></label>
                                <select name="applicant_role" class="form-select" required>
                                    <option value="Pemohon" <?= old('applicant_role') === 'Pemohon' ? 'selected' : '' ?>>Pemohon</option>
                                    <option value="Termohon" <?= old('applicant_role') === 'Termohon' ? 'selected' : '' ?>>Termohon</option>
                                    <option value="Penggugat" <?= old('applicant_role') === 'Penggugat' ? 'selected' : '' ?>>Penggugat</option>
                                    <option value="Tergugat" <?= old('applicant_role') === 'Tergugat' ? 'selected' : '' ?>>Tergugat</option>
                                    <option value="Kuasa Hukum" <?= old('applicant_role') === 'Kuasa Hukum' ? 'selected' : '' ?>>Kuasa Hukum</option>
                                    <option value="Saksi" <?= old('applicant_role') === 'Saksi' ? 'selected' : '' ?>>Saksi</option>
                                    <option value="Lainnya" <?= old('applicant_role') === 'Lainnya' ? 'selected' : '' ?>>Lainnya / Masyarakat Umum</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Instansi / Organisasi (Opsional)</label>
                                <input type="text" name="institution" class="form-control" placeholder="Nama instansi/kantor hukum (jika ada)" value="<?= old('institution') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Alamat Domisili</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap tempat tinggal"><?= old('address') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. DATA PERMINTAAN LAYANAN -->
                    <div class="card card-custom p-4 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <span class="badge bg-success rounded-circle p-2 px-3 fw-bold">2</span>
                            <h5 class="fw-bold mb-0 text-dark">Data Permintaan & Dokumen</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Jenis Layanan <span class="text-danger">*</span></label>
                                <select name="service_id" id="serviceSelect" class="form-select" required>
                                    <option value="">-- Pilih Jenis Layanan --</option>
                                    <?php foreach ($services as $srv): ?>
                                        <option value="<?= $srv['id'] ?>" <?= (old('service_id') == $srv['id'] || (isset($selectedService) && $selectedService['id'] == $srv['id'])) ? 'selected' : '' ?>>
                                            <?= esc($srv['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Sub-Kategori Layanan</label>
                                <select name="sub_service_id" id="subServiceSelect" class="form-select">
                                    <option value="">-- Pilih Sub Kategori (Opsional) --</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Judul / Pokok Keperluan <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" placeholder="Contoh: Konsultasi Persyaratan Gugatan Cerai Gugat / Bantuan e-Court" value="<?= old('subject') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nomor Perkara (Opsional)</label>
                                <input type="text" name="case_number" class="form-control" placeholder="Contoh: 123/Pdt.G/2026/PA.Pnj" value="<?= old('case_number') ?>">
                                <small class="text-muted" style="font-size: 0.72rem;">Isi jika konsultasi terkait perkara yang sudah terdaftar.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Uraian / Deskripsi Permintaan <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan secara ringkas hal atau informasi apa yang ingin Anda tanyakan/konsultasikan..." required><?= old('description') ?></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Unggah Dokumen Pendukung (Opsional)</label>
                                <input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted" style="font-size: 0.72rem;">Format didukung: PDF, JPG, PNG, DOC, XLS. Maksimal 5MB per file. Dapat memilih lebih dari 1 file.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Catatan Tambahan (Opsional)</label>
                                <input type="text" name="notes" class="form-control" placeholder="Catatan tambahan untuk petugas" value="<?= old('notes') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Schedule Selection & Summary -->
                <div class="col-lg-4">
                    <!-- 3. PILIH JADWAL -->
                    <div class="card card-custom p-4 mb-4 sticky-lg-top" style="top: 90px;">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <span class="badge bg-success rounded-circle p-2 px-3 fw-bold">3</span>
                            <h5 class="fw-bold mb-0 text-dark">Pilih Jadwal Konsultasi</h5>
                        </div>

                        <!-- Date Picker -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tanggal Pelayanan <span class="text-danger">*</span></label>
                            <input type="date" id="scheduleDateInput" class="form-control form-control-lg bg-light fw-bold" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                            <small class="text-muted" style="font-size: 0.72rem;">Pelayanan buka Senin s/d Jumat (Kecuali Hari Libur).</small>
                        </div>

                        <!-- Slots Loader & Grid -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small d-flex justify-content-between">
                                <span>Pilih Slot Waktu (WITA) <span class="text-danger">*</span></span>
                                <span id="slotLoadingSpinner" class="spinner-border spinner-border-sm text-success d-none"></span>
                            </label>

                            <input type="hidden" name="schedule_id" id="selectedScheduleId" required>

                            <div id="slotsContainer" class="d-flex flex-column gap-2" style="max-height: 280px; overflow-y: auto;">
                                <div class="text-center py-4 text-muted small">
                                    <div class="spinner-border text-success mb-2" role="status"></div>
                                    <div>Sedang memuat slot jadwal...</div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation & Submit Button -->
                        <div class="pt-3 border-top">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agreementCheck" required>
                                <label class="form-check-label small text-muted" for="agreementCheck">
                                    Saya menyatakan data yang saya isi adalah benar dan bersedia hadir pada jadwal Zoom yang dipilih.
                                </label>
                            </div>

                            <button type="submit" id="btnSubmitApplication" class="btn btn-veronika-primary w-100 py-3 shadow" disabled>
                                <i class="bi bi-send-check-fill me-1"></i> KIRIM PERMOHONAN
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect     = document.getElementById('serviceSelect');
    const subServiceSelect  = document.getElementById('subServiceSelect');
    const dateInput         = document.getElementById('scheduleDateInput');
    const slotsContainer    = document.getElementById('slotsContainer');
    const selectedScheduleId= document.getElementById('selectedScheduleId');
    const btnSubmit         = document.getElementById('btnSubmitApplication');
    const agreementCheck    = document.getElementById('agreementCheck');
    const slotSpinner       = document.getElementById('slotLoadingSpinner');

    // Load Subcategories when service changes
    function loadSubcategories(serviceId) {
        if (!serviceId) {
            subServiceSelect.innerHTML = '<option value="">-- Pilih Sub Kategori (Opsional) --</option>';
            return;
        }

        fetch(`<?= site_url('api/services') ?>/${serviceId}/subcategories`)
            .then(res => res.json())
            .then(res => {
                let options = '<option value="">-- Pilih Sub Kategori (Opsional) --</option>';
                if (res.success && res.data) {
                    res.data.forEach(sub => {
                        options += `<option value="${sub.id}">${sub.name}</option>`;
                    });
                }
                subServiceSelect.innerHTML = options;
            })
            .catch(err => console.error(err));
    }

    serviceSelect.addEventListener('change', function() {
        loadSubcategories(this.value);
    });

    if (serviceSelect.value) {
        loadSubcategories(serviceSelect.value);
    }

    // Load Slots dynamically via AJAX
    function loadSlots(date) {
        slotsContainer.innerHTML = `
            <div class="text-center py-4 text-muted small">
                <div class="spinner-border spinner-border-sm text-success mb-1"></div>
                <div>Sedang memproses jadwal...</div>
            </div>
        `;
        slotSpinner.classList.remove('d-none');
        selectedScheduleId.value = '';
        validateFormReady();

        fetch(`<?= site_url('api/schedules/slots') ?>?date=${date}`)
            .then(res => res.json())
            .then(res => {
                slotSpinner.classList.add('d-none');
                if (!res.success) {
                    slotsContainer.innerHTML = `
                        <div class="alert alert-warning p-3 small mb-0 rounded-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> ${res.message}
                        </div>
                    `;
                    return;
                }

                if (res.slots.length === 0) {
                    slotsContainer.innerHTML = `
                        <div class="alert alert-secondary p-3 small mb-0 rounded-3 text-center">
                            <i class="bi bi-calendar-x me-1"></i> Belum tersedia jadwal pelayanan pada tanggal ini.
                        </div>
                    `;
                    return;
                }

                let html = '';
                res.slots.forEach(slot => {
                    if (slot.is_full) {
                        html += `
                            <div class="p-2 border rounded-3 bg-light text-muted d-flex justify-content-between align-items-center opacity-75">
                                <div>
                                    <i class="bi bi-clock me-1"></i> <strong>${slot.time_formatted} WITA</strong>
                                </div>
                                <span class="badge bg-danger">SLOT PENUH</span>
                            </div>
                        `;
                    } else if (slot.is_past) {
                        html += `
                            <div class="p-2 border rounded-3 bg-light text-muted d-flex justify-content-between align-items-center opacity-50">
                                <div>
                                    <i class="bi bi-clock me-1"></i> <strong>${slot.time_formatted} WITA</strong>
                                </div>
                                <span class="badge bg-secondary">Waktu Terlewat</span>
                            </div>
                        `;
                    } else {
                        html += `
                            <label class="p-2 border rounded-3 d-flex justify-content-between align-items-center cursor-pointer slot-card" style="cursor: pointer; transition: all 0.2s;" data-slot-id="${slot.id}">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="radio" name="slot_radio" value="${slot.id}" class="form-check-input m-0">
                                    <span class="fw-bold text-dark">${slot.time_formatted} WITA</span>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Tersedia</span>
                            </label>
                        `;
                    }
                });

                slotsContainer.innerHTML = html;

                // Add radio select event listener
                document.querySelectorAll('input[name="slot_radio"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        selectedScheduleId.value = this.value;
                        document.querySelectorAll('.slot-card').forEach(el => {
                            el.classList.remove('border-success', 'bg-success-subtle');
                        });
                        this.closest('.slot-card').classList.add('border-success', 'bg-success-subtle');
                        validateFormReady();
                    });
                });
            })
            .catch(err => {
                slotSpinner.classList.add('d-none');
                slotsContainer.innerHTML = `<div class="alert alert-danger p-2 small">Gagal memuat slot. Silakan coba kembali.</div>`;
            });
    }

    dateInput.addEventListener('change', function() {
        loadSlots(this.value);
    });

    loadSlots(dateInput.value);

    // Form ready verification
    function validateFormReady() {
        if (selectedScheduleId.value && agreementCheck.checked) {
            btnSubmit.removeAttribute('disabled');
        } else {
            btnSubmit.setAttribute('disabled', 'disabled');
        }
    }

    agreementCheck.addEventListener('change', validateFormReady);

    // Handle submit loading state
    document.getElementById('applicationForm').addEventListener('submit', function() {
        btnSubmit.setAttribute('disabled', 'disabled');
        btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Sedang memproses...`;
    });
});
</script>
<?= $this->endSection() ?>
