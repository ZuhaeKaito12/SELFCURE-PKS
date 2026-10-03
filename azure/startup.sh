#!/bin/bash
# Startup command for Azure App Service (Linux, PHP 8.x).
# Set it in the portal: Configuration > General settings > Startup Command:
#     bash /home/site/wwwroot/azure/startup.sh

APP_DIR=/home/site/wwwroot
cd "$APP_DIR" || exit 1

# 1. Serve Laravel from /public instead of the project root.
cp "$APP_DIR/azure/nginx-default.conf" /etc/nginx/sites-available/default
service nginx reload

# 2. Database: apply migrations, then the production-safe seeder
#    (reference data + the administrator account; never demo data, and it
#    never rebuilds questions once they exist). Both are safe on every restart.
php artisan migrate --force || echo "WARNING: migrations failed - check the DB_* settings."
php artisan db:seed --force || echo "WARNING: seeding failed - check ADMIN_EMAIL / ADMIN_PASSWORD."

# 3. Production caches (rebuilt on every start so new settings take effect).
php artisan config:cache
php artisan route:cache
php artisan view:cache
