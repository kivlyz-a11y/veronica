<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h3 class="fw-extrabold text-dark mb-0">Pengaturan Sistem</h3>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-semibold">
                <i class="bi bi-shield-check me-1"></i> SI VERONIKA
            </span>
        </div>
        <p class="text-muted small mb-0">Kelola identitas Pengadilan Agama Penajam, kustomisasi template pesan WhatsApp, dan jadwal pengiriman otomatis.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="submit" form="settingsForm" class="btn btn-veronika-primary fw-semibold px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-floppy2-fill"></i> Simpan Semua Perubahan
        </button>
    </div>
</div>

<!-- Main Row with Tabs -->
<div class="row g-4">
    <!-- Left: Settings Nav & Form Tabs -->
    <div class="col-lg-8">
        <form id="settingsForm" action="<?= site_url('admin/settings') ?>" method="POST">
            <?= csrf_field() ?>

            <!-- Navigation Tabs -->
            <ul class="nav nav-pills custom-pills p-2 bg-white rounded-4 shadow-sm mb-4 border" id="settingsTab" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link active w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2 rounded-3" id="tab-gateway-btn" data-bs-toggle="pill" data-bs-target="#tab-gateway" type="button" role="tab">
                        <i class="bi bi-whatsapp fs-5 text-success"></i>
                        <span>WhatsApp Gateway</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2 rounded-3" id="tab-templates-btn" data-bs-toggle="pill" data-bs-target="#tab-templates" type="button" role="tab">
                        <i class="bi bi-chat-square-quote fs-5 text-warning"></i>
                        <span>Template Pesan</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2 rounded-3" id="tab-institution-btn" data-bs-toggle="pill" data-bs-target="#tab-institution" type="button" role="tab">
                        <i class="bi bi-building fs-5 text-primary"></i>
                        <span>Profil Instansi</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2 rounded-3" id="tab-service-btn" data-bs-toggle="pill" data-bs-target="#tab-service" type="button" role="tab">
                        <i class="bi bi-camera-video fs-5 text-info"></i>
                        <span>Zoom & Layanan</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2 rounded-3" id="tab-cron-btn" data-bs-toggle="pill" data-bs-target="#tab-cron" type="button" role="tab">
                        <i class="bi bi-clock-history fs-5 text-secondary"></i>
                        <span>Otomasi / Cron</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="settingsTabContent">

                <!-- ================= TAB 0: WHATSAPP GATEWAY CONFIGURATION ================= -->
                <div class="tab-pane fade show active" id="tab-gateway" role="tabpanel">
                    <div class="card card-panel p-4 p-md-5 mb-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-whatsapp text-success me-2"></i> Konfigurasi Server WhatsApp Gateway
                                </h5>
                                <small class="text-muted">Atur koneksi API WAHA (WhatsApp HTTP API), generic HTTP gateway, atau mode simulasi pengujian.</small>
                            </div>
                        </div>

                        <?php
                        $currentProvider = $settings['wa_provider'] ?? env('WHATSAPP_PROVIDER', 'waha');
                        $currentApiUrl   = $settings['wa_api_url'] ?? env('WHATSAPP_API_URL', 'http://36.95.108.50:3000');
                        $currentApiKey   = $settings['wa_api_key'] ?? env('WHATSAPP_API_KEY', 'secret123');
                        $currentSession  = $settings['wa_session_name'] ?? env('WHATSAPP_SESSION', 'test');
                        $currentSender   = $settings['wa_sender_number'] ?? env('WHATSAPP_SENDER', '6285389705146');
                        $currentTimeout  = $settings['wa_timeout'] ?? env('WHATSAPP_TIMEOUT', '15');
                        ?>

                        <div class="row g-3 small">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Mode Provider WhatsApp <span class="text-danger">*</span></label>
                                <select name="wa_provider" class="form-select" id="wa_provider_select">
                                    <option value="waha" <?= $currentProvider === 'waha' ? 'selected' : '' ?>>WAHA Server (WhatsApp HTTP API)</option>
                                    <option value="http" <?= $currentProvider === 'http' ? 'selected' : '' ?>>Generic HTTP Gateway (Fonnte / Wablas / RuangWA)</option>
                                    <option value="mock" <?= $currentProvider === 'mock' ? 'selected' : '' ?>>Mock / Simulator (Development & Testing - Tanpa Kirim Nyata)</option>
                                </select>
                                <small class="text-muted">Pilih <code>Mock</code> jika server WAHA sedang offline/maintenance agar transaksi tetap berjalan lancar.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Batas Timeout Koneksi</label>
                                <select name="wa_timeout" class="form-select">
                                    <option value="5" <?= $currentTimeout == '5' ? 'selected' : '' ?>>5 Detik (Respons Cepat)</option>
                                    <option value="10" <?= $currentTimeout == '10' ? 'selected' : '' ?>>10 Detik (Direkomendasikan)</option>
                                    <option value="15" <?= $currentTimeout == '15' ? 'selected' : '' ?>>15 Detik</option>
                                    <option value="30" <?= $currentTimeout == '30' ? 'selected' : '' ?>>30 Detik (Maksimal)</option>
                                </select>
                                <small class="text-muted">Batas maksimal menunggu balasan server gateway sebelum dianggap gagal/timeout.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">URL Endpoint API Server <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-hdd-network text-success"></i></span>
                                    <input type="text" name="wa_api_url" class="form-control font-monospace" value="<?= esc($currentApiUrl) ?>" placeholder="http://36.95.108.50:3000 atau https://waha.domainanda.com" required>
                                </div>
                                <small class="text-muted">Contoh: <code>http://36.95.108.50:3000</code>, <code>http://localhost:3000</code>, atau domain reverse proxy.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">API Key / Secret Token</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-key text-muted"></i></span>
                                    <input type="text" name="wa_api_key" class="form-control font-monospace" value="<?= esc($currentApiKey) ?>" placeholder="secret123 atau API token">
                                </div>
                                <small class="text-muted">Sesuai nilai <code>WAHA_API_KEY</code> pada container WAHA.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Nama Sesi (WAHA Session Name)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person-badge text-muted"></i></span>
                                    <input type="text" name="wa_session_name" class="form-control font-monospace" value="<?= esc($currentSession) ?>" placeholder="test atau default">
                                </div>
                                <small class="text-muted">Nama sesi WhatsApp di dashboard WAHA (contoh: <code>test</code> atau <code>default</code>).</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Nomor Bot / WhatsApp Pengirim</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-telephone text-muted"></i></span>
                                    <input type="text" name="wa_sender_number" class="form-control font-monospace" value="<?= esc($currentSender) ?>" placeholder="6285389705146">
                                </div>
                                <small class="text-muted">Nomor WhatsApp resmi Pengadilan Agama Penajam yang terhubung ke bot.</small>
                            </div>
                        </div>

                        <!-- Troubleshooting Alert Box -->
                        <div class="alert alert-warning border-0 rounded-3 mt-4 mb-0 small">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Mengapa Muncul Error "Connection timed out"?</h6>
                            <ul class="mb-0 ps-3">
                                <li>Server WAHA di <code><?= esc($currentApiUrl) ?></code> sedang mati / container docker berhenti.</li>
                                <li>IP Public server WAHA berubah (jika menggunakan dynamic IP tanpa DDNS).</li>
                                <li>Port <code>3000</code> diblokir oleh firewall server / provider internet / belum di-port forward di router.</li>
                                <li><strong>Solusi Cepat:</strong> Ubah mode provider ke <strong>Mock / Simulator</strong> sementara waktu agar pemohon tetap dapat mengajukan permohonan tanpa error terhenti.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- ================= TAB 1: MESSAGE TEMPLATES ================= -->
                <div class="tab-pane fade" id="tab-templates" role="tabpanel">
                    <div class="card card-panel p-4 p-md-5 mb-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-chat-dots-fill text-warning me-2"></i> Kustomisasi Template Pesan WhatsApp
                                </h5>
                                <small class="text-muted">Sesuaikan susunan kata dan kalimat pesan WhatsApp yang dikirimkan otomatis ke pemohon.</small>
                            </div>
                        </div>

                        <!-- Variable Chips Helper -->
                        <div class="p-3 bg-light rounded-4 border mb-4">
                            <div class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1">
                                <i class="bi bi-tags-fill text-success"></i> Klik tag variabel di bawah untuk menyalin ke clipboard:
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <?php
                                $variables = [
                                    '{{nama}}'              => 'Nama Pemohon',
                                    '{{nomor_registrasi}}'  => 'No. Registrasi',
                                    '{{layanan}}'           => 'Nama Layanan',
                                    '{{tanggal}}'           => 'Tanggal Pelayanan',
                                    '{{waktu}}'             => 'Jam Pelayanan (WITA)',
                                    '{{status}}'            => 'Status Verifikasi',
                                    '{{catatan}}'           => 'Catatan Petugas',
                                    '{{link_zoom}}'         => 'Tautan Zoom',
                                    '{{zoom_meeting_id}}'   => 'Meeting ID Zoom',
                                    '{{zoom_password}}'     => 'Passcode Zoom',
                                    '{{url_cek_status}}'    => 'Link Cek Status',
                                    '{{nama_instansi}}'     => 'Pengadilan Agama Penajam',
                                ];
                                foreach ($variables as $vKey => $vDesc):
                                ?>
                                    <button type="button" class="btn btn-sm btn-white border shadow-2xs variable-badge rounded-pill text-dark fw-semibold" onclick="copyVariable('<?= $vKey ?>', this)" title="<?= $vDesc ?>">
                                        <code class="text-success"><?= $vKey ?></code> <small class="text-muted ms-1"><?= $vDesc ?></small>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Template 1: Pendaftaran Baru -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small d-flex justify-content-between">
                                <span>1. Notifikasi Konfirmasi Pendaftaran Berhasil</span>
                                <span class="badge bg-light text-muted border">Terkirim otomatis saat daftar</span>
                            </label>
                            <textarea name="wa_template_registration" class="form-control font-monospace p-3 small" rows="5" style="border-radius: 10px;"><?= esc($settings['wa_template_registration'] ?? '') ?></textarea>
                        </div>

                        <!-- Template 2: Update Status -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small d-flex justify-content-between">
                                <span>2. Notifikasi Perubahan Status Verifikasi / Perbaikan Berkas</span>
                                <span class="badge bg-light text-muted border">Saat petugas verifikasi</span>
                            </label>
                            <textarea name="wa_template_status_update" class="form-control font-monospace p-3 small" rows="5" style="border-radius: 10px;"><?= esc($settings['wa_template_status_update'] ?? '') ?></textarea>
                        </div>

                        <!-- Template 3: Pengiriman Link Zoom -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small d-flex justify-content-between">
                                <span>3. Notifikasi Pengiriman Link Zoom Pertemuan Online</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Menjelang Layanan</span>
                            </label>
                            <textarea name="wa_template_zoom_link" class="form-control font-monospace p-3 small" rows="5" style="border-radius: 10px;"><?= esc($settings['wa_template_zoom_link'] ?? '') ?></textarea>
                        </div>

                        <!-- Template 4 & 5: Pengingat H-1 & H-1 Jam -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">4. Pengingat H-1 (Sehari Sebelumnya)</label>
                                <textarea name="wa_template_reminder_h1" class="form-control font-monospace p-3 small" rows="4" style="border-radius: 10px;"><?= esc($settings['wa_template_reminder_h1'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">5. Pengingat H-1 Jam (Sebelum Sesi Dimulai)</label>
                                <textarea name="wa_template_reminder_h1h" class="form-control font-monospace p-3 small" rows="4" style="border-radius: 10px;"><?= esc($settings['wa_template_reminder_h1h'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 2: INSTITUTION PROFILE ================= -->
                <div class="tab-pane fade" id="tab-institution" role="tabpanel">
                    <div class="card card-panel p-4 p-md-5 mb-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-bank text-primary me-2"></i> Identitas & Profil Instansi
                                </h5>
                                <small class="text-muted">Data instansi resmi yang dicantumkan pada kop surat, tiket PDF, dan landing page.</small>
                            </div>
                        </div>

                        <div class="row g-3 small">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Nama Singkat Aplikasi <span class="text-danger">*</span></label>
                                <input type="text" name="app_name" class="form-control" value="<?= esc($settings['app_name'] ?? 'SI VERONIKA') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Nama Panjang Aplikasi</label>
                                <input type="text" name="app_long_name" class="form-control" value="<?= esc($settings['app_long_name'] ?? 'Sistem Verifikasi Online CEKAdministrasi') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Nama Lembaga / Instansi Peradilan <span class="text-danger">*</span></label>
                                <input type="text" name="institution_name" class="form-control" value="<?= esc($settings['institution_name'] ?? 'Pengadilan Agama Penajam') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Alamat Kantor Lengkap</label>
                                <textarea name="institution_address" class="form-control" rows="2"><?= esc($settings['institution_address'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Nomor Telepon Kantor / Hotline</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-telephone text-muted"></i></span>
                                    <input type="text" name="institution_phone" class="form-control" value="<?= esc($settings['institution_phone'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Alamat Email Resmi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" name="institution_email" class="form-control" value="<?= esc($settings['institution_email'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Website Resmi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-globe text-muted"></i></span>
                                    <input type="url" name="institution_website" class="form-control" value="<?= esc($settings['institution_website'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Jam Operasional Pelayanan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-clock text-muted"></i></span>
                                    <input type="text" name="service_hours" class="form-control" value="<?= esc($settings['service_hours'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 3: ZOOM & SERVICE CONFIGURATION ================= -->
                <div class="tab-pane fade" id="tab-service" role="tabpanel">
                    <div class="card card-panel p-4 p-md-5 mb-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-camera-video-fill text-success me-2"></i> Pengaturan Link Zoom & Batas Unggahan
                                </h5>
                                <small class="text-muted">Konfigurasi otomatisasi pengiriman link Zoom dan ketentuan unggah dokumen permohonan.</small>
                            </div>
                        </div>

                        <div class="row g-3 small">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Waktu Pengiriman Otomatis Link Zoom</label>
                                <select name="zoom_reminder_offset_minutes" class="form-select">
                                    <option value="30" <?= ($settings['zoom_reminder_offset_minutes'] ?? '') == '30' ? 'selected' : '' ?>>30 Menit Sebelum Jadwal</option>
                                    <option value="15" <?= ($settings['zoom_reminder_offset_minutes'] ?? '') == '15' ? 'selected' : '' ?>>15 Menit Sebelum Jadwal</option>
                                    <option value="10" <?= ($settings['zoom_reminder_offset_minutes'] ?? '10') == '10' ? 'selected' : '' ?>>10 Menit Sebelum Jadwal (Standar)</option>
                                    <option value="5" <?= ($settings['zoom_reminder_offset_minutes'] ?? '') == '5' ? 'selected' : '' ?>>5 Menit Sebelum Jadwal</option>
                                    <option value="0" <?= ($settings['zoom_reminder_offset_minutes'] ?? '') == '0' ? 'selected' : '' ?>>Tepat Saat Jadwal Dimulai</option>
                                </select>
                                <small class="text-muted">Scheduler otomatis mengirimkan pesan link Zoom kepada pemohon pada waktu ini.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Batas Ukuran Maksimal Dokumen Unggahan</label>
                                <div class="input-group">
                                    <input type="number" name="max_file_size_kb" class="form-control" value="<?= esc($settings['max_file_size_kb'] ?? '5120') ?>" min="1024" max="20480">
                                    <span class="input-group-text">KB (Kilobytes)</span>
                                </div>
                                <small class="text-muted">Default: 5120 KB (5 MB).</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Tipe Ekstensi File yang Diizinkan</label>
                                <input type="text" name="allowed_file_types" class="form-control font-monospace" value="<?= esc($settings['allowed_file_types'] ?? 'pdf,jpg,jpeg,png,doc,docx,xls,xlsx') ?>">
                                <small class="text-muted">Pisahkan dengan koma (contoh: <code>pdf,jpg,jpeg,png,doc,docx,xls,xlsx</code>).</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 4: CRON JOB & AUTOMATION ================= -->
                <div class="tab-pane fade" id="tab-cron" role="tabpanel">
                    <div class="card card-panel p-4 p-md-5 mb-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-cpu-fill text-info me-2"></i> Otomasi Task & Background Scheduler
                                </h5>
                                <small class="text-muted">Panduan pengaturan cron job pada server agar pengingat terkirim otomatis.</small>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-start gap-3 rounded-4 p-3 mb-4">
                            <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Bagaimana Cara Kerja Otomasi SI VERONIKA?</h6>
                                <p class="small mb-0">
                                    Sistem menggunakan CodeIgniter Spark CLI commands untuk mengecek jadwal pemohon secara periodik. Scheduler akan mengecek siapa saja yang memiliki jadwal esok hari (H-1), satu jam lagi (H-1h), dan mengirimkan tautan Zoom tepat <?= esc($settings['zoom_reminder_offset_minutes'] ?? '10') ?> menit sebelum jadwal dimulai.
                                </p>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark small mb-2">1. Command Pengingat & Pengiriman Link Zoom Otomatis:</h6>
                        <div class="p-3 bg-dark text-light rounded-3 font-monospace small mb-3 position-relative">
                            <code>* * * * * cd <?= FCPATH ?>.. && php spark veronika:send-reminders >> writable/logs/reminder.log 2>&1</code>
                        </div>

                        <h6 class="fw-bold text-dark small mb-2">2. Command Retry Worker Notifikasi Tertunda:</h6>
                        <div class="p-3 bg-dark text-light rounded-3 font-monospace small mb-3 position-relative">
                            <code>*/5 * * * * cd <?= FCPATH ?>.. && php spark veronika:process-notifications >> writable/logs/worker.log 2>&1</code>
                        </div>

                        <h6 class="fw-bold text-dark small mb-2">3. Command Pembersihan File Cache Lama:</h6>
                        <div class="p-3 bg-dark text-light rounded-3 font-monospace small mb-3 position-relative">
                            <code>0 0 * * * cd <?= FCPATH ?>.. && php spark veronika:cleanup-files >> writable/logs/cleanup.log 2>&1</code>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-veronika-primary px-5 py-3 fw-bold rounded-3 shadow">
                    <i class="bi bi-floppy2-fill me-1"></i> SIMPAN SEMUA PENGATURAN
                </button>
            </div>
        </form>
    </div>

    <!-- Right: Live WhatsApp Status & Testing Tool -->
    <div class="col-lg-4">
        <!-- Live Status Card -->
        <div class="card card-panel p-4 mb-4 border-0 shadow-sm">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
                <span><i class="bi bi-broadcast text-success me-2"></i> WhatsApp Gateway</span>
                <?php if ($currentProvider === 'mock'): ?>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">MOCK MODE</span>
                <?php else: ?>
                    <span class="badge bg-success text-white rounded-pill px-3 py-1"><?= strtoupper($currentProvider) ?></span>
                <?php endif; ?>
            </h6>

            <div class="p-3 bg-light rounded-3 mb-3 small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Provider:</span>
                    <strong class="text-dark font-monospace"><?= strtoupper(esc($currentProvider)) ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Server URL:</span>
                    <strong class="text-truncate ms-2 text-dark font-monospace" style="max-width: 170px;" title="<?= esc($currentApiUrl) ?>">
                        <?= esc($currentApiUrl ?: '-') ?>
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Sesi WAHA:</span>
                    <strong class="text-dark font-monospace"><i class="bi bi-person-check text-success me-1"></i><?= esc($currentSession ?: 'default') ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Nomor Bot:</span>
                    <strong class="text-dark font-monospace"><?= esc($currentSender ?: '-') ?></strong>
                </div>
            </div>

            <!-- Action 1: Test Server Connection Ping -->
            <form action="<?= site_url('admin/settings/test-whatsapp') ?>" method="POST" class="mb-3">
                <?= csrf_field() ?>
                <input type="hidden" name="test_phone" value="">
                <button type="submit" class="btn btn-outline-success w-100 fw-bold py-2 rounded-3 small d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-repeat"></i> Cek Status Koneksi Sesi WAHA
                </button>
            </form>

            <hr class="my-3">

            <!-- Action 2: Test Send WhatsApp Message -->
            <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-send-check-fill text-primary me-1"></i> Uji Coba Kirim Pesan:</h6>
            <p class="text-muted small mb-3">Kirimkan pesan instan ke nomor WhatsApp pribadi untuk menguji pengiriman.</p>

            <form action="<?= site_url('admin/settings/test-whatsapp') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Nomor WhatsApp Tujuan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-whatsapp text-success"></i></span>
                        <input type="text" name="test_phone" class="form-control" placeholder="Contoh: 081234567890" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-paperplane-fill"></i> Kirim Pesan Tes Sekarang
                </button>
            </form>
        </div>

        <!-- Quick Help Card -->
        <div class="card card-panel p-4 border-0 shadow-sm bg-success text-white">
            <h6 class="fw-bold mb-2"><i class="bi bi-shield-check me-1"></i> Pengadilan Agama Penajam</h6>
            <p class="small mb-0 opacity-90">
                Aplikasi SI VERONIKA dirancang dengan arsitektur tangguh, menjaga privasi data pencari keadilan dan mempermudah layanan konsultasi online melalui Zoom.
            </p>
        </div>
    </div>
</div>

<style>
/* Custom Styling for Settings Page */
.custom-pills .nav-link {
    color: #4a5568;
    background: transparent;
    transition: all 0.25s ease;
}

.custom-pills .nav-link.active {
    background: #0a5c36 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(10, 92, 54, 0.25);
}

.custom-pills .nav-link:hover:not(.active) {
    background: #f1f5f9;
    color: #0a5c36;
}

.variable-badge {
    transition: all 0.15s ease;
    cursor: pointer;
    font-size: 0.78rem;
}

.variable-badge:hover {
    background: #e2e8f0;
    transform: scale(1.03);
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyVariable(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const origHtml = btn.innerHTML;
        btn.innerHTML = `<span class="text-success"><i class="bi bi-check-lg"></i> Tersalin!</span>`;
        setTimeout(() => {
            btn.innerHTML = origHtml;
        }, 1500);
    });
}
</script>
<?= $this->endSection() ?>
