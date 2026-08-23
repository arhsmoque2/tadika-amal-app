# syntax=docker/dockerfile:1
#
# Production multi-stage Dockerfile for Google Cloud Run (PHP 8.4 + FrankenPHP / Octane)
# Per ADR-0009 and Cloud Run stateless container specification.

# ---- Stage 1: PHP Dependencies ----
FROM composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --ignore-platform-reqs \
    --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# ---- Stage 2: Frontend Assets (Vite) ----
FROM node:22-alpine AS assets
WORKDIR /app

RUN corepack enable && corepack prepare pnpm@9.15.9 --activate

COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile --ignore-scripts

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN pnpm run build

# ---- Stage 3: Production Runtime ----
FROM dunglas/frankenphp:1-php8.4-bookworm AS runtime

# Install production PHP extensions for Neon Postgres, Document Generation, and Filament
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    pdo_sqlite \
    gd \
    zip \
    intl \
    bcmath \
    opcache \
    pcntl

WORKDIR /app

COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

ENV PORT=8080
EXPOSE 8080

CMD ["frankenphp", "php-server", "--listen", ":8080", "--root", "/app/public"]
