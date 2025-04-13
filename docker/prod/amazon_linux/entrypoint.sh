#!/bin/sh
set -e

# PHP-FPM と NGINX を起動
php-fpm --daemonize
nginx -g "daemon off;"
