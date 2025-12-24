#!/bin/sh
# ═══════════════════════════════════════════════════════════════
# TecnoGest - Docker Container Startup Script (Railway)
# ═══════════════════════════════════════════════════════════════

set -e

echo "🚀 Starting TecnoGest on Railway..."
echo "📍 Environment: ${APP_ENV:-production}"
echo "🔌 Port: ${PORT:-8080}"

# Crear directorios necesarios
mkdir -p /var/log/php /var/log/supervisor /var/log/nginx /run/nginx

# Configurar puerto dinámico de Railway en nginx
if [ -n "$PORT" ]; then
    echo "🔧 Configuring nginx for port $PORT..."
    sed -i "s/listen 8080/listen $PORT/g" /etc/nginx/http.d/default.conf
    sed -i "s/listen \[::\]:8080/listen [::]:$PORT/g" /etc/nginx/http.d/default.conf
fi

# Generar APP_KEY si no existe
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Crear enlace simbólico de storage si no existe
if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link || true
fi

# Ejecutar migraciones automáticamente en producción
if [ "$APP_ENV" = "production" ] || [ "$RUN_MIGRATIONS" = "true" ]; then
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
php artisan config:cache || echo "⚠️ Config cache skipped"
php artisan route:cache || echo "⚠️ Route cache skipped"
php artisan view:cache || echo "⚠️ View cache skipped"
php artisan event:cache || echo "⚠️ Event cache skipped"

echo "✅ TecnoGest started successfully on port ${PORT:-8080}!"

# Iniciar Supervisor (gestiona nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
