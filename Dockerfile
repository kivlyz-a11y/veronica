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

# Konfigurasi ServerName dan VirtualHost Apache ke /var/www/html/public
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && mkdir -p /var/run/apache2 /var/lock/apache2 /var/log/apache2

RUN { \
        echo '<VirtualHost *:80>'; \
        echo '    ServerAdmin webmaster@localhost'; \
        echo '    DocumentRoot /var/www/html/public'; \
        echo '    <Directory /var/www/html/public>'; \
        echo '        Options -Indexes +FollowSymLinks'; \
        echo '        AllowOverride All'; \
        echo '        Require all granted'; \
        echo '    </Directory>'; \
        echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
        echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
        echo '</VirtualHost>'; \
    } > /etc/apache2/sites-available/000-default.conf

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
