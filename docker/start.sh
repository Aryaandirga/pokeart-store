#!/bin/bash

# Run migrations
php artisan migrate --force

# Start PHP-FPM foreground
php-fpm -F &

# Tunggu FPM siap
sleep 5

# Cek apakah FPM jalan
php-fpm -t

# Start Nginx foreground
nginx -g "daemon off;"