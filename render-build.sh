#!/usr/bin/env bash
# Exit on error
set -o errexit

# Install PHP dependencies if needed, or let Railway handle Composer
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build Vite assets
npm install
npm run build

# Run Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
