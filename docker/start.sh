#!/bin/bash

echo "Waiting for database..."
sleep 2

# Hapus file cache lama secara paksa
rm -f bootstrap/cache/routes-v7.php
rm -f bootstrap/cache/config.php

# Clear via artisan
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Config cache
php artisan config:cache

# Route cache - jangan pakai cache kalau ada error, pakai langsung saja
echo "=== Route list before cache ==="
php artisan route:list --path=wishlist 2>&1

# Skip route:cache karena bisa menyebabkan route hilang
# php artisan route:cache

php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true

echo "=== Final route check ==="
php artisan route:list --path=wishlist 2>&1

# Scheduler background
(while true; do php artisan schedule:run >> /dev/null 2>&1; sleep 60; done) &

echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=8080
