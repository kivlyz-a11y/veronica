FROM debian:bookworm-slim

# Hindari dialog interaktif apt
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Makassar

# Debian 12 Bookworm menyertakan PHP 8.2 secara native tanpa perlu PPA/GPG eksternal
RUN apt-get update && apt-get install -y --no-install-recommends \
    apache2 \
    libapache2-mod-php \
    php8.2 \
    php8.2-cli \
    php8.2-intl \
    php8.2-gd \
    php8.2-zip \
    php8.2-mysql \
    php8.2-mbstring \
    php8.2-bcmath \
    php8.2-curl \
    php8.2-xml \
    php8.2-opcache \
    git \
    unzip \
    ca-certificates \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer resmi
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Enable Apache rewrite & headers
RUN a2enmod rewrite headers php8.2

# Konfigurasi Apache DocumentRoot ke folder /public milik CodeIgniter 4
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Izinkan .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Konfigurasi php.ini untuk CodeIgniter 4
RUN { \
        echo 'memory_limit = 256M'; \
        echo 'upload_max_filesize = 20M'; \
        echo 'post_max_size = 25M'; \
        echo 'max_execution_time = 120'; \
        echo 'date.timezone = Asia/Makassar'; \
        echo 'opcache.enable = 1'; \
    } > /etc/php/8.2/mods-available/veronika.ini \
    && phpenmod veronika

WORKDIR /var/www/html

# Salin seluruh kode aplikasi
COPY . .

# Install dependensi PHP mode production
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs

# Pastikan folder writable dibuat dengan permission yang benar
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# Salin script entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i -e 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port HTTP 80
EXPOSE 80

# Jalankan entrypoint dan Apache
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2ctl", "-D", "FOREGROUND"]
