#!/bin/bash
set -e

chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan migrate --force

exec "$@"
