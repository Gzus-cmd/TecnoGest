# ═══════════════════════════════════════════════════════════════
# TecnoGest - Dockerfile para Railway
# ═══════════════════════════════════════════════════════════════

# ───────────────────────────────────────────────────────────────
# STAGE 1: Compilar Assets Frontend
# ───────────────────────────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci --legacy-peer-deps || npm install --legacy-peer-deps

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
COPY tailwind.config.js* postcss.config.js* ./

RUN npm run build

# ───────────────────────────────────────────────────────────────
# STAGE 2: Instalar Dependencias PHP
# ───────────────────────────────────────────────────────────────
FROM composer:2.8 AS composer-builder

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# ───────────────────────────────────────────────────────────────
# STAGE 3: Imagen Final
# ───────────────────────────────────────────────────────────────
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# Variables de entorno para Railway
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS="0" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="10000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="192" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10" \
    PORT=8080

# Instalar dependencias del sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    freetype \
    libjpeg-turbo \
    libpng \
    libzip \
    oniguruma \
    icu-libs \
    libintl \
    libpq \
    curl \
    && apk add --no-cache --virtual .build-deps \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    gettext-dev \
    postgresql-dev \
    $PHPIZE_DEPS

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    zip \
    gd \
    mbstring \
    bcmath \
    intl \
    opcache \
    pcntl

# Limpiar dependencias de compilación
RUN apk del .build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

# Copiar configuraciones
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar dependencias de Composer
COPY --from=composer-builder /app/vendor ./vendor

# Copiar código fuente
COPY --chown=www-data:www-data . .

# Copiar assets compilados
COPY --from=node-builder /app/public/build ./public/build

# Crear directorios y permisos
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    /var/log/nginx \
    /var/log/supervisor \
    /run/nginx \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Script de inicio
COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

# Health check para Railway
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:${PORT:-8080}/health || exit 1

# Puerto dinámico (Railway asigna PORT automáticamente)
EXPOSE ${PORT:-8080}

CMD ["/usr/local/bin/start"]
