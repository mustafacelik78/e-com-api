FROM php:8.2-fpm

# Composer'ı yükleme
RUN apt-get update && apt-get install -y unzip curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Çalışma dizini
WORKDIR /var/www

# Gerekli PHP uzantıları
RUN docker-php-ext-install pdo pdo_mysql

# Laravel için eksik dizinleri oluştur ve izinleri ayarla
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
