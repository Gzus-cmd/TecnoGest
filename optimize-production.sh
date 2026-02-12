#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════
# TecnoGest - Script de Optimización para Producción
# Compatible con Railway, Docker y servidores convencionales
# ═══════════════════════════════════════════════════════════════

echo "🚀 Iniciando optimización de Laravel para producción..."
echo ""

# 1. Limpiar cachés existentes
echo "🧹 Limpiando cachés antiguos..."
php artisan optimize:clear 2>/dev/null || true

# 2. Instalar dependencias optimizadas
echo "📦 Optimizando autoloader de Composer..."
composer install --optimize-autoloader --no-dev --no-interaction 2>/dev/null || true

# 3. Compilar assets si no existen
if [ ! -d "public/build" ]; then
    echo "🎨 Compilando assets..."
    npm ci --omit=dev 2>/dev/null || true
    npm run build 2>/dev/null || true
fi

# 4. Generar cachés optimizados
echo "⚡ Generando cachés optimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize 2>/dev/null || true
php artisan icons:cache 2>/dev/null || true

echo ""
echo "✅ Optimización completada!"
echo ""
echo "📊 Estado de cachés:"
php artisan about --only=cache 2>/dev/null || true
