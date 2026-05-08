#!/bin/bash

# Run migrations
php artisan migrate --force

# Start PHP-FPM foreground
php-fpm -F &

# Tunggu FPM siap
sleep 3

# Start Nginx foreground
nginx -g "daemon off;"