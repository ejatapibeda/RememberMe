# syntax=docker/dockerfile:1

# ============================================================
# RememberME - Production image for CapRover (Laravel 12 + Vue 3)
# Single self-contained multi-stage build. CapRover builds this
# directly from the GitHub repo (see captain-definition).
# ============================================================

# ---- Stage 1: build Vue/Vite frontend assets ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---- Stage 2: install PHP (composer) dependencies ----
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --ignore-platform-reqs

# ---- Stage 3: production runtime (PHP-FPM + Nginx + Supervisor) ----
FROM php:8.3-fpm-alpine AS app

# System packages
RUN apk add --no-cache nginx supervisor curl bash \
    && mkdir -p /run/nginx

# PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions pdo_mysql bcmath gd intl opcache zip pcntl exif

# Composer binary (for autoload dump at build time)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Application source (respects .dockerignore)
COPY . .
# Vendor from composer stage
COPY --from=vendor /app/vendor ./vendor
# Freshly built frontend assets
COPY --from=assets /app/public/build ./public/build

# Optimize autoloader with the full source present
RUN composer dump-autoload --no-dev --optimize --no-interaction

# Runtime configuration
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permissions for Laravel writable dirs
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
