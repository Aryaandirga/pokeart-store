#!/bin/bash

# Wait for database to be ready
echo "Waiting for database..."
sleep 2

php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true

# Jalankan scheduler di background (ping database harian)
(while true; do php artisan schedule:run >> /dev/null 2>&1; sleep 60; done) &

echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=8080
