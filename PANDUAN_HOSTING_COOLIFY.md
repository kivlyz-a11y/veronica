# 🚀 Panduan Hosting SI VERONIKA di Coolify

Panduan ini berisi langkah demi langkah untuk melakukan deploy aplikasi **SI VERONIKA (Pengadilan Agama Penajam)** ke server VPS menggunakan **Coolify**.

---

## 📋 Berkas Konfigurasi yang Telah Disiapkan

1. `Dockerfile` - Konfigurasi container PHP 8.2 Apache dengan ekstensi lengkap (`intl`, `mbstring`, `gd`, `zip`, `mysqli`, `pdo_mysql`, `opcache`) dan DocumentRoot diarahkan ke `public/`.
2. `docker-entrypoint.sh` - Script otomatis untuk memastikan permission folder `writable/` dan menjalankan migrasi database otomatis.
3. `.dockerignore` - Optimasi build image agar cepat dan bersih dari file sampah/lokal.
4. `docker-compose.yml` - Opsi deployment satu paket (Aplikasi + MariaDB/MySQL).
5. `.env.production.example` - Contoh variabel lingkungan untuk production di Coolify.

---

## 🌟 Metode 1: Deploy Melalui Git Repository (Sangat Direkomendasikan)

Metode ini adalah cara standar di Coolify: Anda menghubungkan repository GitHub/GitLab, dan Coolify akan otomatis melakukan build & deploy setiap kali ada update (CI/CD).

### Langkah 1: Push Source Code ke Git
Pastikan source code project Anda sudah di-push ke GitHub / GitLab:
```bash
git add .
git commit -m "Siapkan konfigurasi Docker & Coolify"
git push origin main
```

---

### Langkah 2: Buat Database di Coolify
1. Buka dashboard **Coolify** Anda.
2. Masuk ke Project / Environment Anda.
3. Klik **+ New Resource** -> pilih **Databases** -> pilih **MySQL** atau **MariaDB**.
4. Beri nama database, misalnya `veronika-db`.
5. Catat kredensial yang dibuat Coolify:
   - **Internal Host**: (biasanya nama service atau IP internal container, misal: `veronika-db` atau `<uuid>`)
   - **Database Name**: `si_veronika`
   - **User**: `veronika_user`
   - **Password**: `<password_tergenerate>`
   - **Port**: `3306`

---

### Langkah 3: Buat Application di Coolify
1. Di project yang sama, klik **+ New Resource** -> pilih **Application**.
2. Pilih **Public Repository** atau **Private Repository (GitHub App)**.
3. Masukkan URL repository SI VERONIKA Anda.
4. Pada pilihan **Build Pack**, Coolify akan otomatis mendeteksi **Dockerfile**.
5. Di bagian **Ports Exposes**, masukkan:
   ```
   80
   ```
6. Di bagian **Domains**, masukkan domain/subdomain Anda (misal: `https://veronika.pa-penajam.go.id`).

---

### Langkah 4: Tambahkan Persistent Storage (PENTING!)
Agar berkas upload pemohon dan file session tidak hilang saat container di-redeploy/update:
1. Buka aplikasi Anda di Coolify -> menu **Storages** / **Persistent Storage**.
2. Tambahkan storage baru:
   - **Volume Name**: `veronika_writable`
   - **Mount Path**: `/var/www/html/writable`
3. Klik **Save**.

---

### Langkah 5: Atur Environment Variables di Coolify
Buka menu **Environment Variables** di Coolify, lalu masukkan variabel berikut:

| Key | Value Contoh | Keterangan |
|---|---|---|
| `CI_ENVIRONMENT` | `production` | Mode production |
| `app.baseURL` | `https://veronika.pa-penajam.go.id/` | Domain Anda (wajib diakhiri `/`) |
| `app.indexPage` | | Kosongkan |
| `app.appTimezone` | `Asia/Makassar` | Zona waktu WITA |
| `database.default.hostname` | `veronika-db` | Host internal DB Coolify |
| `database.default.database` | `si_veronika` | Nama database |
| `database.default.username` | `veronika_user` | User database |
| `database.default.password` | `password_db_anda` | Password database |
| `database.default.DBDriver` | `MySQLi` | Driver MySQLi |
| `database.default.port` | `3306` | Port DB |
| `AUTO_MIGRATE` | `true` | Menjalankan migrasi tabel otomatis saat container start |
| `WHATSAPP_PROVIDER` | `waha` | Gateway WhatsApp (`waha` / `mock` / `http`) |
| `WHATSAPP_API_URL` | `http://36.95.108.50:3000` | URL Server WhatsApp Gateway |
| `WHATSAPP_API_KEY` | `secret123` | API Key WAHA |
| `WHATSAPP_SESSION` | `test` | Session Name WAHA |
| `WHATSAPP_SENDER` | `6285389705146` | Nomor WA Pengirim |

---

### Langkah 6: Deploy & Inisialisasi Data Awal
1. Klik tombol **Deploy** di pojok kanan atas Coolify.
2. Tunggu proses build Docker selesai sampai status menjadi **Running (Healthy)**.
3. **Inisialisasi Akun Admin Default & Data Master**:
   - Di dashboard aplikasi Coolify, buka tab **Terminal** / **Exec Console**.
   - Jalankan perintah seeder CodeIgniter:
     ```bash
     php spark db:seed InitialSeeder
     ```
4. Akun default yang terbentuk:
   - **Username**: `admin`
   - **Password**: `admin123` *(Segera ganti setelah login pertama kali!)*

---

## 🐳 Metode 2: Deploy Melalui Docker Compose (Semua dalam 1 Stack)

Jika Anda ingin aplikasi dan database MariaDB berjalan dalam satu kesatuan konfigurasi:

1. Di Coolify, klik **+ New Resource** -> **Docker Compose**.
2. Tempelkan isi dari berkas [`docker-compose.yml`](./docker-compose.yml).
3. Isi nilai Environment Variables yang diperlukan.
4. Klik **Deploy**.

---

## ⏰ Pengaturan Scheduled Task / Cron Job (Pembersihan Cache Otomatis)

SI VERONIKA memiliki command bawaan untuk membersihkan file ekspor/cache temporary yang sudah berumur > 24 jam.

Untuk menjalankannya otomatis setiap malam di Coolify:
1. Buka aplikasi Anda di Coolify -> menu **Scheduled Tasks** / **Cron Jobs**.
2. Klik **+ Add Task**.
3. Atur:
   - **Name**: `Cleanup Veronika Cache`
   - **Frequency (Cron Expression)**: `0 2 * * *` *(Setiap jam 02.00 WITA)*
   - **Command**: `php /var/www/html/spark veronika:cleanup-files`
4. Klik **Save**.

---

## 🔒 Konfigurasi Domain & SSL (HTTPS)

- Coolify secara otomatis mengintegrasikan **Traefik Reverse Proxy** dan **Let's Encrypt SSL**.
- Cukup arahkan DNS Domain Anda:
  - **A Record**: `veronika.domainanda.com` -> `IP Public VPS Coolify`
- Setelah DNS terhubung, masukkan domain dengan format `https://veronika.domainanda.com` di menu General Settings Coolify, SSL akan aktif secara otomatis.

---

## 🛠️ Troubleshooting & Tips

1. **Halaman CSS/JS Rusak atau Link Error 404?**
   - Pastikan `app.baseURL` di Environment Variables sesuai dengan domain dan protokol HTTPS serta diakhiri garis miring `/` (contoh: `https://veronika.pa-penajam.go.id/`).

2. **Error Database Connection?**
   - Pastikan `database.default.hostname` menggunakan nama host container database di Coolify (bukan `localhost`).

3. **Upload File Gagal / Error Permission?**
   - Pastikan Persistent Volume terpasang di `/var/www/html/writable`. Container sudah otomatis menjalankan `chmod -R 775` dan `chown -R www-data:www-data` saat booting.

4. **Koneksi WhatsApp Gateway?**
   - Jika server WAHA berada di server VPS yang sama, Anda bisa menggunakan IP Docker atau network yang sama, atau menggunakan URL public IP WAHA.
