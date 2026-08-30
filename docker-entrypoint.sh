#!/bin/bash
set -e

# Pastikan folder runtime Apache siap
mkdir -p /var/run/apache2 /var/lock/apache2 /var/log/apache2
chown -R www-data:www-data /var/run/apache2 /var/lock/apache2 /var/log/apache2 2>/dev/null || true

# Pastikan folder writable dan sub-foldernya ada saat persistent volume terpasang
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads \
         /var/www/html/writable/debugbar

# Set kepemilikan dan hak akses writable
chown -R www-data:www-data /var/www/html/writable 2>/dev/null || true
chmod -R 775 /var/www/html/writable 2>/dev/null || true

# Jika AUTO_MIGRATE atau DB_AUTO_MIGRATE diaktifkan, jalankan migrasi database otomatis
if [ "$AUTO_MIGRATE" = "true" ] || [ "$AUTO_MIGRATE" = "1" ] || [ "$DB_AUTO_MIGRATE" = "true" ] || [ "$DB_AUTO_MIGRATE" = "1" ]; then
    echo "[SI VERONIKA] Menjalankan migrasi database otomatis..."
    php /var/www/html/spark migrate --force || true
fi

# Jalankan perintah utama kontainer (Apache)
exec "$@"
