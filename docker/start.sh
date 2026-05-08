#!/bin/bash

echo "Waiting for database..."
sleep 2

# Clear semua cache lama dulu
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true

# Scheduler background
(while true; do php artisan schedule:run >> /dev/null 2>&1; sleep 60; done) &

echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=8080
