#!/bin/bash
set -e

# Pastikan folder writable dan sub-foldernya ada jika volume persisten terpasang
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads \
         /var/www/html/writable/debugbar

# Set hak akses writable
chmod -R 775 /var/www/html/writable || true

# Jika AUTO_MIGRATE diaktifkan, jalankan spark migrate
if [ "$AUTO_MIGRATE" = "true" ] || [ "$AUTO_MIGRATE" = "1" ]; then
    echo "[SI VERONIKA] Menjalankan migrasi database otomatis..."
    php /var/www/html/spark migrate --force || echo "[SI VERONIKA] Migrasi database dilewati / belum terhubung."
fi
