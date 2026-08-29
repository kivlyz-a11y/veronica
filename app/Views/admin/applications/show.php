<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="<?= site_url('admin/applications') ?>" class="text-success text-decoration-none">Permohonan</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($app['registration_number']) ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3">
            <h3 class="fw-bold text-dark mb-0"><?= esc($app['registration_number']) ?></h3>
            <div><?= status_badge($app['status']) ?></div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="<?= site_url('unduh-bukti/' . $app['registration_number']) ?>" class="btn btn-danger btn-sm text-white px-3 py-2 rounded-2">
            <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Bukti PDF
        </a>
        <a href="<?= site_url('admin/applications') ?>" class="btn btn-light btn-sm border px-3 py-2 rounded-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Details & Verification -->
    <div class="col-lg-8">
        <!-- Applicant & Request Card -->
        <div class="card card-panel p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-lines-fill text-success me-2"></i> Data Identitas Pemohon</h5>
                <span class="badge bg-light text-secondary border">Kode Booking: <?= esc($app['booking_code']) ?></span>
            </div>

            <div class="row g-3 small">
                <div class="col-md-6">
                    <span class="text-muted d-block">Nama Lengkap:</span>
                    <strong class="text-dark fs-6"><?= esc($app['applicant_name']) ?></strong>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Nomor Induk Kependudukan (NIK):</span>
                    <strong class="text-dark"><?= esc($app['applicant_nik']) ?></strong>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Nomor WhatsApp:</span>
                    <strong class="text-dark"><i class="bi bi-whatsapp text-success me-1"></i><?= esc($app['applicant_phone']) ?></strong>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Email:</span>
                    <strong class="text-dark"><?= esc($app['applicant_email'] ?: '-') ?></strong>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Status Pemohon:</span>
                    <strong class="text-dark"><?= esc($app['applicant_role']) ?></strong>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Instansi / Organisasi:</span>
                    <strong class="text-dark"><?= esc($app['applicant_institution'] ?: '-') ?></strong>
                </div>
                <div class="col-12">
                    <span class="text-muted d-block">Alamat Domisili:</span>
                    <div class="text-dark"><?= esc($app['applicant_address'] ?: '-') ?></div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-text-fill text-primary me-2"></i> Rincian Permintaan Layanan</h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1"><?= esc($app['service_name']) ?></span>
            </div>

            <div class="row g-3 small">
                <?php if (!empty($app['sub_service_name'])): ?>
                <div class="col-md-6">
                    <span class="text-muted d-block">Sub-Kategori:</span>
                    <strong class="text-dark"><?= esc($app['sub_service_name']) ?></strong>
                </div>
                <?php endif; ?>
                <?php if (!empty($app['case_number'])): ?>
                <div class="col-md-6">
                    <span class="text-muted d-block">Nomor Perkara:</span>
                    <strong class="text-dark text-primary"><?= esc($app['case_number']) ?></strong>
                </div>
                <?php endif; ?>
                <div class="col-12">
                    <span class="text-muted d-block">Pokok / Judul Keperluan:</span>
                    <strong class="text-dark"><?= esc($app['subject']) ?></strong>
                </div>
                <div class="col-12">
                    <span class="text-muted d-block">Uraian / Deskripsi Lengkap:</span>
                    <div class="p-3 bg-light rounded-3 text-dark mt-1" style="white-space: pre-line;"><?= esc($app['description']) ?></div>
                </div>
                <?php if (!empty($app['notes'])): ?>
                <div class="col-12">
                    <span class="text-muted d-block">Catatan Pemohon:</span>
                    <div class="text-muted fst-italic"><?= esc($app['notes']) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Documents Section -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-paperclip me-1 text-secondary"></i> Dokumen Pendukung Diunggah:</h6>
                <?php if (empty($documents)): ?>
                    <p class="text-muted small mb-0">Tidak ada dokumen yang dilampirkan oleh pemohon.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($documents as $doc): ?>
                            <div class="p-2 border rounded-3 d-flex justify-content-between align-items-center bg-light small">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                    <div>
                                        <div class="fw-semibold text-dark"><?= esc($doc['filename']) ?></div>
                                        <small class="text-muted"><?= format_filesize($doc['size']) ?> • <?= esc($doc['mime_type']) ?></small>
                                    </div>
                                </div>
                                <a href="<?= base_url('writable/' . $doc['filepath']) ?>" target="_blank" class="btn btn-sm btn-outline-primary px-3 py-1">
                                    <i class="bi bi-download me-1"></i> Unduh
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Verification Actions Form -->
        <div class="card card-panel p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-success me-2"></i> Verifikasi Administrasi & Perubahan Status</h5>
            
            <form action="<?= site_url('admin/applications/' . $app['id'] . '/status') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Ubah Status Permohonan <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <?php
                            $statuses = [
                                'Menunggu Verifikasi',
                                'Sedang Diverifikasi',
                                'Disetujui',
                                'Perlu Perbaikan',
                                'Ditolak',
                                'Terjadwal',
                                'Sedang Berlangsung',
                                'Selesai',
                                'Dibatalkan',
                                'Tidak Hadir'
                            ];
                            foreach ($statuses as $st):
                            ?>
                                <option value="<?= $st ?>" <?= $app['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Catatan Verifikasi / Alasan Perubahan Status</label>
                        <textarea name="verification_notes" class="form-control" rows="3" placeholder="Tuliskan instruksi perbaikan atau catatan untuk pemohon..."><?= esc($app['verification_notes'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_whatsapp" id="sendWaCheck" value="1" checked>
                            <label class="form-check-label small fw-semibold text-dark" for="sendWaCheck">
                                <i class="bi bi-whatsapp text-success me-1"></i> Kirim notifikasi WhatsApp otomatis ke pemohon terkait status ini
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Simpan & Perbarui Status
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Notification History -->
        <div class="card card-panel p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-whatsapp text-success me-2"></i> Riwayat Pengiriman Notifikasi WhatsApp</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Tipe</th>
                            <th>Penerima</th>
                            <th>Status</th>
                            <th>Waktu Kirim</th>
                            <th>Percobaan</th>
                            <th>Pesan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($notifications)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada riwayat notifikasi WhatsApp.</td></tr>
                        <?php else: ?>
                            <?php foreach ($notifications as $ntf): ?>
                                <tr>
                                    <td><code><?= esc($ntf['type']) ?></code></td>
                                    <td><?= esc($ntf['recipient']) ?></td>
                                    <td>
                                        <?php if ($ntf['status'] === 'sent'): ?>
                                            <span class="badge bg-success">Terkirim</span>
                                        <?php elseif ($ntf['status'] === 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Gagal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $ntf['sent_at'] ? date('d/m/Y H:i', strtotime($ntf['sent_at'])) : '-' ?></td>
                                    <td><?= $ntf['attempts'] ?>x</td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-outline-secondary p-1" data-bs-toggle="modal" data-bs-target="#msgModal<?= $ntf['id'] ?>">
                                            <i class="bi bi-eye"></i> Lihat
                                        </button>

                                        <!-- Message Content Modal -->
                                        <div class="modal fade" id="msgModal<?= $ntf['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold">Detail Pesan WhatsApp</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="p-3 bg-light rounded-3 text-dark small" style="white-space: pre-line;"><?= esc($ntf['message']) ?></div>
                                                        <?php if (!empty($ntf['error_message'])): ?>
                                                            <div class="alert alert-danger small mt-2 mb-0">
                                                                <strong>Error:</strong> <?= esc($ntf['error_message']) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Audit Trail -->
        <div class="card card-panel p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text text-secondary me-2"></i> Catatan Riwayat Aktivitas (Audit Trail)</h5>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                <?php if (empty($auditLogs)): ?>
                    <li class="text-muted">Belum ada riwayat perubahan.</li>
                <?php else: ?>
                    <?php foreach ($auditLogs as $log): ?>
                        <li class="d-flex gap-3 border-bottom pb-2">
                            <div class="text-muted" style="min-width: 130px; font-size: 0.75rem;">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark border me-1"><?= esc($log['action']) ?></span>
                                <span class="text-dark"><?= esc($log['description']) ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Right Column: Zoom & Schedule Management -->
    <div class="col-lg-4">
        <!-- Schedule Info Card -->
        <div class="card card-panel p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar3 text-primary me-2"></i> Jadwal Konsultasi</h5>
            <div class="p-3 rounded-3 bg-light border mb-3">
                <div class="small text-muted">Tanggal:</div>
                <div class="fw-bold text-dark fs-6"><?= format_indo_date($app['schedule_date']) ?></div>
                <div class="small text-muted mt-2">Waktu Pelayanan:</div>
                <div class="fw-bold text-success fs-5"><?= substr($app['schedule_start_time'], 0, 5) ?> - <?= substr($app['schedule_end_time'], 0, 5) ?> WITA</div>
            </div>

            <div class="d-flex flex-column gap-1 small text-muted">
                <div><strong>Check-In:</strong> <?= $app['check_in_at'] ? date('d/m/Y H:i', strtotime($app['check_in_at'])) : '<span class="text-danger">Belum Check-In</span>' ?></div>
                <div><strong>Mulai Layanan:</strong> <?= $app['service_started_at'] ? date('d/m/Y H:i', strtotime($app['service_started_at'])) : '-' ?></div>
                <div><strong>Selesai:</strong> <?= $app['service_ended_at'] ? date('d/m/Y H:i', strtotime($app['service_ended_at'])) : '-' ?></div>
            </div>
        </div>

        <!-- Zoom Meeting Management Card -->
        <div class="card card-panel p-4 mb-4 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-camera-video-fill text-success me-2"></i> Link Zoom Meeting</h5>
                <?php if (!empty($app['zoom_url'])): ?>
                    <span class="badge bg-success">Tersedia</span>
                <?php else: ?>
                    <span class="badge bg-danger">Belum Ada</span>
                <?php endif; ?>
            </div>

            <?php if (empty($app['zoom_url'])): ?>
                <div class="alert alert-warning small p-2 rounded-2 mb-3">
                    <i class="bi bi-exclamation-triangle me-1"></i> Link Zoom belum tersedia. Silakan masukkan link Zoom resmi sebelum mengirimkan notifikasi kepada pemohon.
                </div>
            <?php else: ?>
                <div class="mb-3 p-3 bg-light rounded-3 small">
                    <div class="text-truncate mb-1">
                        <strong>URL:</strong> <a href="<?= esc($app['zoom_url']) ?>" target="_blank"><?= esc($app['zoom_url']) ?></a>
                    </div>
                    <?php if (!empty($app['zoom_meeting_id'])): ?>
                        <div><strong>Meeting ID:</strong> <?= esc($app['zoom_meeting_id']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($app['zoom_password'])): ?>
                        <div><strong>Passcode:</strong> <?= esc($app['zoom_password']) ?></div>
                    <?php endif; ?>
                    <div class="text-muted mt-2" style="font-size: 0.72rem;">
                        Terakhir diupdate: <?= $app['zoom_added_at'] ? date('d/m/Y H:i', strtotime($app['zoom_added_at'])) : '-' ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Input/Edit Zoom Form -->
            <form action="<?= site_url('admin/applications/' . $app['id'] . '/zoom') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-2">
                    <label class="form-label small fw-semibold">Tautan / Link Zoom <span class="text-danger">*</span></label>
                    <input type="url" name="zoom_url" class="form-control form-control-sm" placeholder="https://us04web.zoom.us/j/..." value="<?= esc($app['zoom_url'] ?? '') ?>" required>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Meeting ID</label>
                        <input type="text" name="zoom_meeting_id" class="form-control form-control-sm" placeholder="842 1234 5678" value="<?= esc($app['zoom_meeting_id'] ?? '') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Passcode</label>
                        <input type="text" name="zoom_password" class="form-control form-control-sm" placeholder="123456" value="<?= esc($app['zoom_password'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan Zoom (Opsional)</label>
                    <input type="text" name="zoom_notes" class="form-control form-control-sm" placeholder="Gunakan nama sesuai KTP saat join" value="<?= esc($app['zoom_notes'] ?? '') ?>">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="send_wa_now" id="sendWaNow" value="1">
                    <label class="form-check-label small fw-semibold text-dark" for="sendWaNow">
                        Kirim link Zoom sekarang via WhatsApp
                    </label>
                </div>

                <button type="submit" class="btn btn-veronika-primary w-100 py-2 small fw-semibold">
                    <i class="bi bi-save me-1"></i> <?= empty($app['zoom_url']) ? 'Tambah Link Zoom' : 'Ubah Link Zoom' ?>
                </button>
            </form>
        </div>

        <!-- Manual Resend Notification Options -->
        <div class="card card-panel p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-send-fill text-primary me-2"></i> Kirim Ulang Pesan WhatsApp:</h6>
            <div class="d-flex flex-column gap-2">
                <form action="<?= site_url('admin/applications/' . $app['id'] . '/resend/confirmation') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100 text-start py-2">
                        <i class="bi bi-arrow-repeat me-1 text-success"></i> Kirim Ulang Konfirmasi Pendaftaran
                    </button>
                </form>

                <form action="<?= site_url('admin/applications/' . $app['id'] . '/resend/zoom') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-success btn-sm w-100 text-start py-2" <?= empty($app['zoom_url']) ? 'disabled' : '' ?>>
                        <i class="bi bi-camera-video me-1"></i> Kirim Ulang Link Zoom
                    </button>
                </form>

                <form action="<?= site_url('admin/applications/' . $app['id'] . '/resend/h1') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-warning btn-sm w-100 text-start py-2 text-dark">
                        <i class="bi bi-bell me-1 text-warning"></i> Kirim Pengingat H-1
                    </button>
                </form>

                <form action="<?= site_url('admin/applications/' . $app['id'] . '/resend/h1h') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-info btn-sm w-100 text-start py-2 text-dark">
                        <i class="bi bi-alarm me-1 text-info"></i> Kirim Pengingat H-1 Jam
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
