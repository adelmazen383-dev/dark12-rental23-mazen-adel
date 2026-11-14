#!/bin/bash
set -e

# Setup .env if missing
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Ensure database exists
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    chmod 664 database/database.sqlite
    php artisan migrate --force --seed
fi

# Generate APP_KEY if missing or empty
php artisan key:generate --force || true

# Storage link
php artisan storage:link || true

# Support dynamic PORT assigned by cloud hosts (Render, Railway, Heroku)
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
fi

exec apache2-foreground
