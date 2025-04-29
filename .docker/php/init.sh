#!/bin/sh
chown -R www-data:www-data /var/www/app
chmod -R 755 /var/www/app/storage

cd /var/www/app && php artisan serve --host=0.0.0.0 --port=8080
