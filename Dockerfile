FROM serversideup/php:8.2-apache

# Konfigurasi Apache DocumentRoot ke folder /public milik CodeIgniter 4
ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV PHP_TIMEZONE=Asia/Makassar
ENV PHP_MEMORY_LIMIT=256M
ENV PHP_POST_MAX_SIZE=25M
ENV PHP_UPLOAD_MAX_FILESIZE=20M
ENV PHP_MAX_EXECUTION_TIME=120
ENV PHP_OPCACHE_ENABLE=1

# Beralih ke user root untuk permission dan setup berkas
USER root

WORKDIR /var/www/html

# Salin file composer terlebih dahulu untuk caching layer
COPY --chown=www-data:www-data composer.json composer.lock* ./

# Install dependensi PHP production secara instan (semua ekstensi intl, gd, zip, mysqli dll sudah bawaan)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Salin seluruh source code proyek
COPY --chown=www-data:www-data . .

# Buat folder writable jika belum ada dan set permission
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# Pasang script inisialisasi boot (dijalankan otomatis oleh s6-overlay saat start)
COPY docker-entrypoint.sh /etc/entrypoint.d/99-veronika.sh
RUN sed -i -e 's/\r$//' /etc/entrypoint.d/99-veronika.sh \
    && chmod +x /etc/entrypoint.d/99-veronika.sh

# Beralih kembali ke user aman www-data
USER www-data

# Port default yang digunakan serversideup adalah 8080
EXPOSE 8080
