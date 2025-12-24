#!/bin/sh
# ═══════════════════════════════════════════════════════════════
# TecnoGest - Docker Container Startup Script (Render)
# ═══════════════════════════════════════════════════════════════

set -e

echo "🚀 Starting TecnoGest..."

# Crear directorios necesarios
mkdir -p /var/log/php /var/log/supervisor /var/log/nginx

# Ejecutar migraciones automáticamente en producción
if [ "$APP_ENV" = "production" ]; then
    echo "📊 Running database migrations..."
    php artisan migrate --force || echo "⚠️ Migrations skipped or already up to date"
fi

# Ejecutar seeders si es necesario
if [ "$RUN_SEEDERS" = "true" ]; then
    echo "🌱 Running database seeders..."
    php artisan db:seed --class=ProductionSeeder --force || echo "⚠️ Seeders skipped"
fi

# Limpiar y cachear configuración
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ TecnoGest started successfully!"

# Iniciar Supervisor (gestiona nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
