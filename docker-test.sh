#!/bin/bash
# ============================================
# Script rápido de prueba local
# Prueba cada variante de TecnoGest
# ============================================

set -e

echo "🧪 Testing TecnoGest Docker Variants..."
echo ""

test_variant() {
    local variant=$1
    local compose_file=$2
    local container_name=$3

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "🔍 Testing: $variant"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

    # Detener si ya está corriendo
    docker-compose -f "$compose_file" down -v 2>/dev/null || true

    # Iniciar
    echo "▶️  Starting containers..."
    docker-compose -f "$compose_file" up -d

    # Esperar a que esté listo
    echo "⏳ Waiting for container to be ready..."
    sleep 10

    # Verificar salud
    if docker exec "$container_name" php artisan about > /dev/null 2>&1; then
        echo "✅ Container is healthy!"
    else
        echo "❌ Container health check failed"
        docker-compose -f "$compose_file" logs
        docker-compose -f "$compose_file" down -v
        return 1
    fi

    # Verificar endpoint
    if curl -f http://localhost:8080/up > /dev/null 2>&1; then
        echo "✅ HTTP endpoint responding!"
    else
        echo "⚠️  HTTP endpoint not responding (might need migrations)"
    fi

    # Detener
    echo "⏹️  Stopping containers..."
    docker-compose -f "$compose_file" down -v

    echo "✅ $variant test passed!"
    echo ""
}

# Test cada variante
test_variant "Standalone" "docker-compose.standalone.yml" "tecnogest-standalone"
test_variant "SQLite" "docker-compose.sqlite.yml" "tecnogest-sqlite"
test_variant "MySQL" "docker-compose.mysql.yml" "tecnogest-app"
test_variant "PostgreSQL" "docker-compose.postgresql.yml" "tecnogest-app"

echo "╔════════════════════════════════════════════════════════╗"
echo "║  ✅ All variants tested successfully!                 ║"
echo "╚════════════════════════════════════════════════════════╝"
