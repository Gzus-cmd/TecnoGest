#!/bin/sh
# ═══════════════════════════════════════════════════════════════
# TecnoGest - Docker Container Startup Script
# Soporte para múltiples entornos y bases de datos
# ═══════════════════════════════════════════════════════════════

set -e

echo "🚀 Starting TecnoGest..."
echo "📍 Environment: ${APP_ENV:-production}"
echo "🗄️  Database: ${DB_CONNECTION:-mysql}"

# ── Crear directorios necesarios ──
mkdir -p /var/log/php /var/log/supervisor /var/log/nginx /run/nginx
mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/logs bootstrap/cache

# ── Permisos ──
chown -R www:www storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache

# ── SQLite: crear archivo si no existe ──
if [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "📦 Configuring SQLite database..."
    mkdir -p database
    DB_FILE="${DB_DATABASE:-/var/www/html/database/tecnogest.sqlite}"
    if [ ! -f "$DB_FILE" ]; then
        touch "$DB_FILE"
        chown www:www "$DB_FILE" 2>/dev/null || true
        chmod 664 "$DB_FILE"
    fi
fi

# ── Esperar a la base de datos (MySQL/PostgreSQL) ──
if [ "$DB_CONNECTION" = "mysql" ] || [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "⏳ Waiting for database..."
    attempt=0
    max_attempts=30

    while [ $attempt -lt $max_attempts ]; do
        if php artisan db:monitor > /dev/null 2>&1; then
            echo "✅ Database is ready!"
            break
        fi
        attempt=$((attempt + 1))
        echo "   Attempt $attempt/$max_attempts..."
        sleep 2
    done

    if [ $attempt -eq $max_attempts ]; then
        echo "⚠️  Database connection timeout - continuing anyway..."
    fi
fi

# ── Generar APP_KEY si no existe ──
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:YOUR_KEY_HERE" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# ── Storage link ──
if [ ! -L "public/storage" ]; then
    php artisan storage:link 2>/dev/null || true
fi

# ── Optimizaciones de producción ──
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing for production..."
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
    php artisan filament:optimize 2>/dev/null || true
    php artisan icons:cache 2>/dev/null || true
fi

# ── Migraciones automáticas ──
if [ "$AUTO_MIGRATE" = "true" ]; then
    echo "📊 Running migrations..."
    php artisan migrate --force
fi

# ── Seeders automáticos ──
if [ "$AUTO_SEED" = "true" ]; then
    echo "🌱 Running seeders..."
    php artisan db:seed --class="${SEEDER_CLASS:-DemoSeeder}" --force
fi

echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║         ✨ TecnoGest is ready!                          ║"
echo "║                                                          ║"
echo "║  🌐 Access: http://localhost:8080                       ║"
echo "║  📧 Default: admin@tecnogest.com / password             ║"
echo "║                                                          ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""

# Iniciar Supervisor (gestiona Nginx + PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
