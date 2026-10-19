# 🗄️ Panduan Langkah-Langkah Perubahan Database (SQL)
### SI VERONICA - Pengadilan Agama Penajam

Dokumen ini berisi panduan teknis langkah demi langkah untuk menerapkan perubahan database pada aplikasi **SI VERONICA**, baik di server lokal (XAMPP), staging, maupun server production (VPS / Coolify).

---

## 📌 Ringkasan Perubahan Database

Pada pembaruan ini:
- **Struktur Tabel (DDL)**: Tidak ada perubahan skema tabel (tidak ada tabel baru atau kolom yang diubah). Fitur jadwal berulang memanfaatkan kolom tabel `schedules` yang sudah ada.
- **Data Konfigurasi (DML)**: 
  1. Nilai `app_name` pada tabel `system_settings` diubah dari `SI VERONIKA` menjadi `SI VERONICA`.
  2. Seluruh template notifikasi WhatsApp (`wa_template_*`) pada tabel `system_settings` diperbarui dari `SI VERONIKA` menjadi `SI VERONICA`.
  3. Riwayat pesan pada tabel `notifications` diperbarui ke `SI VERONICA`.

---

## 🚀 Cara 1: Otomatis via CodeIgniter Spark CLI (Sangat Direkomendasikan)

Jika Anda menggunakan terminal (lokal atau SSH server), CodeIgniter telah menyediakan migrasi otomatis resmi.

### 1. Periksa Status Migrasi
Jalankan perintah berikut pada direktori project:
```bash
php spark migrate:status
```
Anda akan melihat migrasi `UpdateAppNameAndTemplatesToVeronica` berstatus belum dimigrasi (`---`).

### 2. Jalankan Migrasi
```bash
php spark migrate
```
**Output sukses:**
```text
Running all new migrations...
    Running: (App) 2026-10-20-000001_App\Database\Migrations\UpdateAppNameAndTemplatesToVeronica
Migrations complete.
```

*(Catatan: Pada deployment Coolify / Docker, perintah ini otomatis dijalankan saat container di-deploy jika variabel `AUTO_MIGRATE=true` aktif).*

---

## 💻 Cara 2: Manual via phpMyAdmin

Bagi pengguna XAMPP atau cPanel dengan antarmuka web phpMyAdmin:

1. Buka peramban dan masuk ke **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Klik nama database Anda di panel kiri (misalnya: `si_veronika`).
3. Klik tab **SQL** pada menu navigasi atas.
4. Salin dan tempel kueri SQL berikut ke dalam kotak teks:

```sql
-- 1. Perbarui Nama Aplikasi
UPDATE system_settings 
SET setting_value = 'SI VERONICA',
    updated_at = NOW()
WHERE setting_key = 'app_name';

-- 2. Perbarui Seluruh Template WhatsApp
UPDATE system_settings 
SET setting_value = REPLACE(setting_value, 'SI VERONIKA', 'SI VERONICA'),
    updated_at = NOW()
WHERE setting_value LIKE '%SI VERONIKA%';

-- 3. Perbarui Riwayat Notifikasi Pesan (Opsional)
UPDATE notifications 
SET message = REPLACE(message, 'SI VERONIKA', 'SI VERONICA'),
    updated_at = NOW()
WHERE message LIKE '%SI VERONIKA%';
```

5. Klik tombol **Kirim** / **Go** di sudut kanan bawah.
6. Catat pesan sukses bahwa baris konfigurasi telah berhasil diperbarui.

---

## 🖥️ Cara 3: Manual via Terminal / Command Prompt (MySQL CLI)

Jalankan perintah berikut langsung dari terminal:

### Untuk Sistem Operasi Windows (XAMPP):
```bash
mysql -u root si_veronika -e "UPDATE system_settings SET setting_value = 'SI VERONICA', updated_at = NOW() WHERE setting_key = 'app_name'; UPDATE system_settings SET setting_value = REPLACE(setting_value, 'SI VERONIKA', 'SI VERONICA'), updated_at = NOW() WHERE setting_value LIKE '%SI VERONIKA%'; UPDATE notifications SET message = REPLACE(message, 'SI VERONIKA', 'SI VERONICA'), updated_at = NOW() WHERE message LIKE '%SI VERONIKA%';"
```

### Atau Menggunakan Berkas SQL yang Tersedia:
Berkas SQL mentah telah disediakan pada direktori:
📂 `database/migration_update_veronica.sql`

Jalankan perintah import:
```bash
mysql -u root -p si_veronika < database/migration_update_veronica.sql
```

---

## 🔍 Langkah Verifikasi Perubahan

Untuk memastikan bahwa data di database sudah sepenuhnya terbarukan, jalankan kueri pengecekan berikut:

```sql
-- 1. Cek nama aplikasi:
SELECT setting_key, setting_value, updated_at 
FROM system_settings 
WHERE setting_key = 'app_name';

-- 2. Cek pratinjau template pesan WhatsApp:
SELECT setting_key, LEFT(setting_value, 60) AS template_preview 
FROM system_settings 
WHERE setting_key LIKE 'wa_template%';

-- 3. Pastikan tidak ada lagi nama lama yang tersisa:
SELECT COUNT(*) AS total_sisa_veronika 
FROM system_settings 
WHERE setting_value LIKE '%VERONIKA%';
```
*Hasil `total_sisa_veronika` harus bernilai `0`.*

---

## ⏪ Langkah Rollback (Jika Diperlukan)

Jika sewaktu-waktu ingin mengembalikan nilai ke kondisi sebelumnya:

### Menggunakan CodeIgniter Spark:
```bash
php spark migrate:rollback
```

### Menggunakan Kueri SQL Manual:
```sql
UPDATE system_settings 
SET setting_value = 'SI VERONIKA', updated_at = NOW() 
WHERE setting_key = 'app_name';

UPDATE system_settings 
SET setting_value = REPLACE(setting_value, 'SI VERONICA', 'SI VERONIKA'), updated_at = NOW() 
WHERE setting_value LIKE '%SI VERONICA%';

UPDATE notifications 
SET message = REPLACE(message, 'SI VERONICA', 'SI VERONIKA'), updated_at = NOW() 
WHERE message LIKE '%SI VERONICA%';
```
