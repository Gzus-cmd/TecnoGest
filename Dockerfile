# ═══════════════════════════════════════════════════════════════
# TecnoGest - Dockerfile Multi-Stage Optimizado
# PHP 8.4 + Nginx + Supervisor | Producción
# ═══════════════════════════════════════════════════════════════

# ==================== Stage 1: Dependencias PHP ====================
FROM php:8.4-fpm-alpine AS php-deps

# Instalar dependencias de compilación y extensiones PHP
RUN apk add --no-cache --virtual .build-deps \
    libpng-dev libzip-dev oniguruma-dev icu-dev \
    freetype-dev libjpeg-turbo-dev libwebp-dev \
    postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql pdo_pgsql mysqli pgsql \
    mbstring exif pcntl bcmath gd zip intl opcache \
    && apk del .build-deps

# ==================== Stage 2: Builder ====================
FROM php-deps AS builder

# Instalar herramientas de build
RUN apk add --no-cache git curl zip unzip nodejs npm

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos de dependencias primero (mejor cache de Docker layers)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY package.json package-lock.json ./
RUN npm ci --omit=dev

# Copiar código fuente
COPY . .

# Generar autoloader optimizado y compilar assets
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && npm run build \
    && rm -rf node_modules tests storage/framework/testing \
    .git .github .env.* docker-compose*.yml *.md

# ==================== Stage 3: Runtime ====================
FROM php:8.4-fpm-alpine AS runtime

LABEL maintainer="Gzus-cmd"
LABEL version="2.0"
LABEL description="TecnoGest - Sistema de Gestión de Inventario Tecnológico"

# Instalar solo dependencias de runtime (NO recompilar extensiones)
RUN apk add --no-cache \
    libpng libzip oniguruma icu-libs \
    freetype libjpeg-turbo libwebp \
    libpq \
    nginx supervisor bash curl

# Copiar extensiones PHP compiladas desde php-deps (no recompilar)
COPY --from=php-deps /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-deps /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copiar configuraciones
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Crear usuario www
RUN addgroup -g 1000 www && adduser -D -u 1000 -G www www

WORKDIR /var/www/html

# Copiar aplicación desde builder
COPY --from=builder --chown=www:www /var/www/html /var/www/html

# Crear directorios y permisos
RUN mkdir -p \
    storage/framework/{cache/data,sessions,views} \
    storage/logs bootstrap/cache \
    /var/log/php /var/log/supervisor /var/log/nginx /run/nginx \
    && chown -R www:www storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Script de inicio
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

CMD ["/start.sh"]
