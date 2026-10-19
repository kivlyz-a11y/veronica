# 🏛️ SI VERONICA - Sistem Verifikasi Online CEKAdministrasi
### Pengadilan Agama Penajam Kelas II

[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/Framework-CodeIgniter%204.7.4-EF4223?logo=codeigniter&logoColor=white)](https://codeigniter.com)
[![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap%205.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Database](https://img.shields.io/badge/Database-MySQL%20%7C%20MariaDB-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Docker](https://img.shields.io/badge/Deployment-Docker%20%26%20Coolify-2496ED?logo=docker&logoColor=white)](https://coolify.io)

**SI VERONICA** (*Sistem Verifikasi Online CEKAdministrasi*) adalah aplikasi pelayanan terpadu satu pintu (PTSP) online dan konsultasi peradilan berbasis web yang dikembangkan khusus untuk **Pengadilan Agama Penajam**. Aplikasi ini memfasilitasi masyarakat pencari keadilan dalam mengajukan permohonan layanan administrasi peradilan secara transparan, mudah, dan terintegrasi langsung dengan konsultasi daring via Zoom Meeting serta notifikasi otomatis WhatsApp Gateway.

---

## ✨ Fitur Utama

1. **Portal Publik Pemohon:**
   - Katalog jenis layanan administrasi dan informasi persyaratan dokumen.
   - Formulir permohonan daring dengan fitur upload berkas pendukung (PDF/JPG/PNG/DOCX).
   - Pemilihan jadwal dan slot konsultasi online secara *real-time*.
   - Bukti tanda pendaftaran elektronik dilengkapi QR Code verifikasi dan unduh tiket PDF.
   - Pelacakan status permohonan (Cek Status) berbasis nomor registrasi dan nomor WhatsApp.

2. **Integrasi WhatsApp Gateway Otomatis (WAHA):**
   - Notifikasi instan saat pendaftaran berhasil (Nomor Registrasi, Jadwal, Petunjuk).
   - Notifikasi pembaruan status berkas (*Disetujui*, *Perlu Perbaikan*, *Ditolak*).
   - Pengingat otomatis H-1 dan H-1 Jam sebelum sesi konsultasi online.
   - Pengiriman tautan Zoom Meeting, ID & Passcode otomatis 10 menit sebelum jam pelayanan dimulai.

3. **Check-In & QR Code Scanner Petugas:**
   - Pemindai kamera QR Code terintegrasi (`html5-qrcode`) untuk validasi kehadiran pemohon secara cepat di meja PTSP Online.
   - Tombol cepat buka tautan Zoom dan konfirmasi kehadiran.

4. **Panel Administrasi Petugas (Dashboard Admin):**
   - Ringkasan statistik permohonan, tren layanan, dan grafik interaktif (Chart.js).
   - Manajemen permohonan berkas, verifikasi dokumen, input link Zoom, dan pencatatan catatan perbaikan.
   - Manajemen kalender jadwal, kapasitas slot per jam, serta penetapan hari libur nasional.
   - Manajemen pengguna berbasis peran (*Super Admin*, *Admin Pelayanan*, *Petugas PTSP*, *Pimpinan*).
   - Rekapitulasi laporan pelayanan dengan ekspor data ke format **Excel (XLSX)** dan **PDF**.
   - Pengaturan konfigurasi instansi, gateway WAHA, dan kustomisasi template pesan WhatsApp.
   - Audit Trail (Log Aktivitas) lengkap untuk transparansi operasional.

---

## 🛠️ Kebutuhan Sistem

- **PHP**: Versi 8.1, 8.2, atau 8.3
- **Ekstensi PHP**: `intl`, `mbstring`, `mysqli`, `curl`, `gd`, `zip`, `xml`
- **Database**: MySQL 5.7+ atau MariaDB 10.4+
- **Composer**: Versi 2.x
- **WhatsApp Gateway (Opsional)**: WAHA (*WhatsApp HTTP API*) atau Mock Provider bawaan

---

## 🚀 Panduan Instalasi Lokal

### 1. Clone Repository
```bash
git clone https://github.com/kivlyz-a11y/veronica.git
cd veronica
```

### 2. Pasang Dependensi
```bash
composer install
```

### 3. Konfigurasi Lingkungan (.env)
Salin berkas `env` ke `.env`:
```bash
cp env .env
```
Sesuaikan konfigurasi koneksi database pada `.env`:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'
app.appTimezone = 'Asia/Makassar'

database.default.hostname = localhost
database.default.database = si_veronika
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

### 4. Migrasi & Seeding Database
Jalankan migrasi tabel dan data awal (layanan, akun petugas, konfigurasi default):
```bash
php spark migrate
php spark db:seed InitialSeeder
```

### 5. Jalankan Server Pengembangan
```bash
php spark serve --port 8080
```
Buka peramban Anda di [http://localhost:8080](http://localhost:8080).

---

## 👤 Kredensial Login Default

URL Login Petugas: `http://localhost:8080/auth/login`

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `superadmin@pa-penajam.go.id` | `admin123` |
| **Admin Pelayanan** | `admin@pa-penajam.go.id` | `admin123` |
| **Petugas PTSP Online** | `petugas@pa-penajam.go.id` | `petugas123` |
| **Pimpinan (Ketua)** | `pimpinan@pa-penajam.go.id` | `pimpinan123` |

---

## ⏰ Background Scheduler & Automation

Untuk menjalankan pengingat WhatsApp dan pembersihan berkas secara otomatis, tambahkan perintah berikut pada Cron Job server Anda:

```bash
# Pengingat jadwal (H-1, H-1 Jam, dan Link Zoom 10 menit sebelum mulai) - Setiap menit:
* * * * * cd /path/to/veronica && php spark veronica:send-reminders >> writable/logs/reminder.log 2>&1

# Pemrosesan antrean notifikasi tertunda / retry worker - Setiap 5 menit:
*/5 * * * * cd /path/to/veronica && php spark veronica:process-notifications >> writable/logs/worker.log 2>&1

# Pembersihan berkas laporan temporary lama - Setiap tengah malam:
0 0 * * * cd /path/to/veronica && php spark veronica:cleanup-files >> writable/logs/cleanup.log 2>&1
```

*(Catatan: Alias legacy `veronika:*` tetap didukung untuk kompatibilitas ke belakang).*

---

## 🐳 Deployment Docker & Coolify

Aplikasi ini sudah dilengkapi dengan berkas konfigurasi container siap pakai:
- `Dockerfile` (PHP 8.2 Apache + Ekstensi Lengkap)
- `docker-compose.yml` (Aplikasi + MariaDB)
- `docker-entrypoint.sh` (Auto-permissions & auto-migration)
- `.env.production.example` (Template environment production)

Panduan lengkap deployment ke VPS melalui platform Coolify dapat dibaca pada:
📄 **[PANDUAN_HOSTING_COOLIFY.md](PANDUAN_HOSTING_COOLIFY.md)**

---

## 🧪 Pengujian Unit (Automated Tests)

Jalankan seluruh rangkaian automated tests PHPUnit:
```bash
php vendor/phpunit/phpunit/phpunit
```

---

## 📄 Lisensi

Aplikasi ini dikembangkan untuk Pengadilan Agama Penajam Kelas II dan didistribusikan di bawah lisensi [MIT License](LICENSE).
