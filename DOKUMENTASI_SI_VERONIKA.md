# DOKUMENTASI LENGKAP PENGEMBANGAN & PANDUAN SI VERONIKA
## Pengadilan Agama Penajam

Dokumen ini berisi rangkuman lengkap seluruh percakapan, arsitektur, panduan instalasi, kredensial login, dan petunjuk operasional aplikasi **SI VERONIKA (Sistem Verifikasi Online CEKAdministrasi)**.

---

## 1. Spesifikasi Sistem
* **Framework**: CodeIgniter 4 (v4.7.4)
* **Bahasa & Runtime**: PHP 8.3 / 8.1+
* **Database**: MySQL / MariaDB (`si_veronika`)
* **Timezone**: `Asia/Makassar` (WITA)
* **Frontend**: Bootstrap 5 + Bootstrap Icons + Chart.js + html5-qrcode
* **PDF Engine**: `dompdf/dompdf`
* **Excel Engine**: `phpoffice/phpspreadsheet`
* **QR Code**: `chillerlan/php-qrcode` (SVG Vector Generator)
* **WhatsApp Gateway**: WAHA (WhatsApp HTTP API Server)

---

## 2. Kredensial Login Petugas (Default)
URL Login: **http://localhost:8080/auth/login**

| Peran (Role) | Alamat Email | Kata Sandi | Nama Pengguna |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@pa-penajam.go.id` | `admin123` | Super Administrator |
| **Admin Pelayanan** | `admin@pa-penajam.go.id` | `admin123` | Admin Pelayanan |
| **Petugas PTSP Online** | `petugas@pa-penajam.go.id` | `petugas123` | Ahmad Fauzi, S.H. |
| **Pimpinan** | `pimpinan@pa-penajam.go.id` | `pimpinan123` | Drs. H. M. Said, M.H. (Ketua) |

---

## 3. Konfigurasi WhatsApp Gateway (WAHA)
Sistem telah terhubung dengan server WAHA:
* **Provider Mode**: `waha`
* **Server URL**: `http://36.95.108.50:3000`
* **Session Name**: `test`
* **API Key**: `secret123`
* **Nomor Bot Pengirim**: `6285389705146`

File konfigurasi berada di: `c:\xampp\htdocs\veronica\.env`

---

## 4. Mekanisme Pengiriman WhatsApp Otomatis
1. **Otomatis Real-time (Saat Ada Aksi)**:
   - **Saat Pemohon Mendaftar**: Mengirimkan nomor registrasi `VER-YYYYMMDD-0001`, tanggal & jam layanan.
   - **Saat Petugas Mengubah Status**: Mengirimkan update status verifikasi berkas & instruksi perbaikan.
   - **Saat Petugas Menyimpan Link Zoom**: Mengirimkan link Zoom, Meeting ID, dan Passcode.
2. **Otomatis Berdasarkan Waktu (Scheduler Background)**:
   - **Pengingat H-1**: Terkirim otomatis sehari sebelum jadwal konsultasi.
   - **Pengingat H-1 Jam**: Terkirim otomatis 1 jam sebelum jadwal dimulai.
   - **Link Zoom 10 Menit Sebelum Jadwal**: Tautan Zoom otomatis disiarkan ke pemohon 10 menit sebelum jam pelayanan tanpa perlu petugas menekan tombol kirim lagi.

---

## 5. Perintah CLI / Spark untuk Background Automation
Jalankan perintah ini di terminal:
```bash
# Pengecekan dan pengiriman pengingat otomatis (H-1, H-1h, Link Zoom):
php spark veronika:send-reminders

# Pemrosesan antrean pesan WhatsApp yang tertunda:
php spark veronika:process-notifications

# Pembersihan file temporary / cache lama:
php spark veronika:cleanup-files
```

**Setup Cron Job di Server Linux / cPanel (Setiap Menit):**
```bash
* * * * * cd /xampp/htdocs/veronica && php spark veronika:send-reminders >> writable/logs/reminder.log 2>&1
```

---

## 6. Struktur URL Halaman Aplikasi
* **Beranda Publik**: `http://localhost:8080/`
* **Formulir Pendaftaran**: `http://localhost:8080/ajukan-permintaan`
* **Cek Status Permohonan**: `http://localhost:8080/cek-status`
* **Portal Login Petugas**: `http://localhost:8080/auth/login`
* **Dashboard Admin**: `http://localhost:8080/admin/dashboard`
* **Daftar Permohonan**: `http://localhost:8080/admin/applications`
* **Manajemen Jadwal & Slot**: `http://localhost:8080/admin/schedules`
* **Katalog Layanan**: `http://localhost:8080/admin/services`
* **Manajemen Pengguna**: `http://localhost:8080/admin/users`
* **Laporan & Rekapitulasi (PDF/Excel)**: `http://localhost:8080/admin/reports`
* **Pengaturan Sistem & Template WA**: `http://localhost:8080/admin/settings`
* **Audit Trail Log**: `http://localhost:8080/admin/audit-logs`
* **Pelayanan & Check-In QR Scanner**: `http://localhost:8080/officer/checkin`

---
*Dokumen ini dibuat otomatis sebagai arsip permanen percakapan dan panduan aplikasi SI VERONIKA.*
