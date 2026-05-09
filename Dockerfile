FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    curl zip unzip git \
    libpq-dev libpng-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring gd zip

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Prevent UTF-8 BOM from breaking php artisan commands (Railway/Linux can surface BOM from tooling or cached layers).
RUN php docker/strip-php-bom.php

RUN php artisan wayfinder:generate && npm install && npm run build
RUN php artisan route:clear || true
RUN php artisan config:clear || true

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy start script v3
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080
CMD ["/start.sh"]
