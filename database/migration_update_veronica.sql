-- ====================================================================
-- SKRIP PERUBAHAN DATABASE SI VERONICA
-- Pengadilan Agama Penajam Kelas II
-- Tanggal: 20 Oktober 2026
-- Keterangan: Pembaruan Nama Sistem & Template WhatsApp dari SI VERONIKA ke SI VERONICA
-- ====================================================================

-- 1. Pastikan menggunakan database yang sesuai
-- USE si_veronika;

-- --------------------------------------------------------------------
-- 2. Pembaruan Nama Singkat Aplikasi pada Tabel system_settings
-- --------------------------------------------------------------------
UPDATE system_settings 
SET setting_value = 'SI VERONICA',
    updated_at = NOW()
WHERE setting_key = 'app_name';

-- --------------------------------------------------------------------
-- 3. Pembaruan Seluruh Template Pesan WhatsApp pada system_settings
--    (Template Pendaftaran, Status Update, Link Zoom, Pengingat H-1, dan H-1 Jam)
-- --------------------------------------------------------------------
UPDATE system_settings 
SET setting_value = REPLACE(setting_value, 'SI VERONIKA', 'SI VERONICA'),
    updated_at = NOW()
WHERE setting_value LIKE '%SI VERONIKA%';

-- --------------------------------------------------------------------
-- 4. Pembaruan Riwayat Pesan WhatsApp pada Tabel notifications (Jika Ada)
-- --------------------------------------------------------------------
UPDATE notifications 
SET message = REPLACE(message, 'SI VERONIKA', 'SI VERONICA'),
    updated_at = NOW()
WHERE message LIKE '%SI VERONIKA%';

-- ====================================================================
-- QUERY VERIFIKASI (Jalankan untuk memastikan perubahan sudah berhasil)
-- ====================================================================

-- Cek nilai nama aplikasi:
SELECT setting_key, setting_value, updated_at 
FROM system_settings 
WHERE setting_key = 'app_name';

-- Cek seluruh template WhatsApp:
SELECT setting_key, LEFT(setting_value, 50) AS template_preview, updated_at 
FROM system_settings 
WHERE setting_key LIKE 'wa_template%';

-- Pastikan tidak ada lagi teks 'VERONIKA' yang tersisa di system_settings:
SELECT setting_key, setting_value 
FROM system_settings 
WHERE setting_value LIKE '%VERONIKA%';
