#!/bin/bash
set -e

# Pastikan folder writable dan sub-foldernya ada jika menggunakan persistent volume Coolify
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads \
         /var/www/html/writable/debugbar

# Set kepemilikan dan hak akses writable
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

# Jika AUTO_MIGRATE diaktifkan di environment variable, jalankan migrasi database
if [ "$AUTO_MIGRATE" = "true" ] || [ "$AUTO_MIGRATE" = "1" ]; then
    echo "[SI VERONIKA] Menjalankan migrasi database otomatis..."
    php /var/www/html/spark migrate --force || echo "[SI VERONIKA] Migrasi database dilewati / belum terhubung."
fi

# Jalankan perintah utama kontainer
exec "$@"
