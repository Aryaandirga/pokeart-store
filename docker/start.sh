#!/bin/bash

# Wait for database to be ready
echo "Waiting for database..."
sleep 2

php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true

echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=8080
