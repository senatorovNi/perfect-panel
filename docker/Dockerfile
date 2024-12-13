FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY ../ .

# Установка прав
RUN chown -R www-data:www-data /var/www

EXPOSE 9000  # Открываем порт 9000 для PHP-FPM

ENTRYPOINT ["php-fpm"]
