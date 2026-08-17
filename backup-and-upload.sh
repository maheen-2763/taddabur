#!/bin/bash
cd /var/www/taddabur
php artisan backup:run
rclone copy storage/app/private/Taddabur/ gdrive:Backups/ --min-age 1m
