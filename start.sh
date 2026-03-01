#!/bin/sh
php artisan config:clear
php artisan cache:clear
php artisan serve --host=0.0.0.0 --port=10000