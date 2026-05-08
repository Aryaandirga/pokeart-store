#!/bin/bash

# Run migrations
php artisan migrate --force

# Start PHP-FPM sebagai foreground (bukan daemon)
php-fpm -F &

# Start Nginx foreground
nginx -g "daemon off;"