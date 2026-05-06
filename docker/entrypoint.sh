#!/bin/sh
set -e

echo "🚀 Starting Apotek POS Demo..."

# Create storage directories if not exist
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/log/supervisor

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cache config & routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (auto-migrate on deploy)
php artisan migrate --force --no-interaction

# Seed if database is empty (first deploy)
php artisan db:seed --force --no-interaction 2>/dev/null || true

echo "✅ Apotek POS Demo ready!"

exec "$@"
