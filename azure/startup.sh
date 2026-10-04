#!/bin/bash
# Startup command for Azure App Service (Linux, PHP 8.x).
# Set it in the portal: Configuration > General settings > Startup Command:
#     bash /home/site/wwwroot/azure/startup.sh

APP_DIR=/home/site/wwwroot
cd "$APP_DIR" || exit 1

# 1. Serve Laravel from /public instead of the project root.
cp "$APP_DIR/azure/nginx-default.conf" /etc/nginx/sites-available/default
service nginx reload

# 2. Cache the configuration first, so the current app settings are used
#    from the very first request.
php artisan config:cache

# 3. Everything else runs in the background. PHP only starts serving pages
#    once this script ends, and running these steps one after another used
#    to keep visitors on a "502 Bad Gateway" page for about 30 seconds after
#    every cold start. Migrations and the production-safe seeder (reference
#    data + the administrator account, never demo data, never rebuilds
#    questions once they exist) are safe on every restart.
(
    php artisan migrate --force || echo "WARNING: migrations failed - check the DB_* settings."
    php artisan db:seed --force || echo "WARNING: seeding failed - check ADMIN_EMAIL / ADMIN_PASSWORD."
    php artisan route:cache
    php artisan view:cache
) &
