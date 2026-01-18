#!/bin/bash

# Script de optimización para producción en Railway
# Ejecuta todos los comandos de caché de Laravel para máximo rendimiento

echo "🚀 Iniciando optimización de Laravel para producción..."

# Limpiar cachés existentes
echo "🧹 Limpiando cachés antiguos..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generar nuevos cachés optimizados
echo "⚡ Generando cachés optimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
php artisan icons:cache

# Optimizar el autoloader de Composer
echo "📦 Optimizando autoloader de Composer..."
composer install --optimize-autoloader --no-dev

# Optimizar assets si no están en producción
if [ ! -d "public/build" ]; then
    echo "🎨 Compilando assets..."
    npm run build
fi

echo "✅ Optimización completada!"
echo ""
echo "📊 Estado de cachés:"
php artisan about --only=cache

echo ""
echo "💡 Consejos adicionales:"
echo "  - Verifica que APP_ENV=production en .env"
echo "  - Asegúrate de tener APP_DEBUG=false"
echo "  - Configura CACHE_DRIVER=database o redis"
echo "  - Configura SESSION_DRIVER=database o redis"
