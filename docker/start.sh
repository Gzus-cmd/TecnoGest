#!/bin/sh
# ═══════════════════════════════════════════════════════════════
# TecnoGest - Docker Container Startup Script
# ═══════════════════════════════════════════════════════════════

set -e

echo "🚀 Starting TecnoGest..."

# Crear directorios de logs si no existen
mkdir -p /var/log/php /var/log/supervisor

# Esperar a que la base de datos esté lista (si existe)
if [ ! -z "$DB_HOST" ]; then
    echo "⏳ Waiting for database at $DB_HOST:$DB_PORT..."
    timeout=30
    while ! nc -z $DB_HOST $DB_PORT; do
        timeout=$((timeout - 1))
        if [ $timeout -le 0 ]; then
            echo "❌ Database connection timeout"
            exit 1
        fi
        sleep 1
    done
    echo "✅ Database is ready"
fi

# Ejecutar migraciones si es primera vez
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "📊 Running database migrations..."
    php artisan migrate --force
fi

# Ejecutar seeders si es necesario
if [ "$RUN_SEEDERS" = "true" ]; then
    echo "🌱 Running database seeders..."
    php artisan db:seed --class=ProductionSeeder --force
fi

# Limpiar y cachear configuración
echo "⚡ Optimizing application..."
php artisan optimize

echo "✅ TecnoGest started successfully!"
echo "🌐 Access the application at http://localhost:8080"

# Iniciar Supervisor (gestiona nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf