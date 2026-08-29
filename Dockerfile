FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies sistem yang dibutuhkan untuk ekstensi PHP CodeIgniter 4
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        intl \
        mbstring \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        opcache \
        bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite & mod_headers
RUN a2enmod rewrite headers

# Konfigurasi Apache DocumentRoot ke folder /public milik CodeIgniter 4
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Izinkan .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Konfigurasi PHP Production
RUN { \
        echo 'memory_limit = 256M'; \
        echo 'upload_max_filesize = 20M'; \
        echo 'post_max_size = 25M'; \
        echo 'max_execution_time = 120'; \
        echo 'date.timezone = Asia/Makassar'; \
        echo 'opcache.enable = 1'; \
        echo 'opcache.memory_consumption = 128'; \
        echo 'opcache.interned_strings_buffer = 8'; \
        echo 'opcache.max_accelerated_files = 4000'; \
        echo 'opcache.validate_timestamps = 1'; \
    } > /usr/local/etc/php/conf.d/veronika-custom.ini

# Copy composer files terlebih dahulu untuk cache layer Docker
COPY composer.json composer.lock* ./

# Jalankan composer install (production mode)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy seluruh source code aplikasi
COPY . .

# Buat dan pastikan direktori writable ada dengan permission yang benar
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# Salin script entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80 untuk web traffic
EXPOSE 80

# Jalankan entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
