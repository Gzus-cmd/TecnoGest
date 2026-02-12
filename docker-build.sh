#!/bin/bash
# ============================================
# Script para construir y publicar todas las
# variantes de TecnoGest en Docker Hub
# ============================================

set -e

DOCKER_USERNAME="gzus07"
IMAGE_NAME="tecnogest"
VERSION="2.0"

echo "🐳 Building TecnoGest Docker Images..."
echo "📦 Version: $VERSION"
echo ""

# Función para build y tag
build_and_tag() {
    local variant=$1
    local compose_file=$2

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "🔨 Building: $variant"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

    # Build usando docker-compose
    if [ -n "$compose_file" ]; then
        docker-compose -f "$compose_file" build
    else
        docker build -t $DOCKER_USERNAME/$IMAGE_NAME:$variant .
    fi

    # Tags adicionales
    docker tag $DOCKER_USERNAME/$IMAGE_NAME:$variant $DOCKER_USERNAME/$IMAGE_NAME:$variant-$VERSION

    # Tag latest solo para MySQL
    if [ "$variant" = "mysql" ]; then
        docker tag $DOCKER_USERNAME/$IMAGE_NAME:$variant $DOCKER_USERNAME/$IMAGE_NAME:latest
    fi

    echo "✅ Built: $variant"
    echo ""
}

# Verificar que Docker está corriendo
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker no está corriendo"
    exit 1
fi

# Login a Docker Hub
echo "🔐 Login to Docker Hub..."
docker login

echo ""
echo "🏗️  Building all variants..."
echo ""

# Build todas las variantes
build_and_tag "standalone" "docker-compose.standalone.yml"
build_and_tag "sqlite" "docker-compose.sqlite.yml"
build_and_tag "mysql" "docker-compose.mysql.yml"
build_and_tag "postgresql" "docker-compose.postgresql.yml"

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ All images built successfully!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Mostrar imágenes creadas
echo "📋 Created images:"
docker images | grep $DOCKER_USERNAME/$IMAGE_NAME

echo ""
read -p "📤 Push images to Docker Hub? (y/n) " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "📤 Pushing images to Docker Hub..."
    echo ""

    # Push todas las variantes
    docker push $DOCKER_USERNAME/$IMAGE_NAME:standalone
    docker push $DOCKER_USERNAME/$IMAGE_NAME:standalone-$VERSION

    docker push $DOCKER_USERNAME/$IMAGE_NAME:sqlite
    docker push $DOCKER_USERNAME/$IMAGE_NAME:sqlite-$VERSION

    docker push $DOCKER_USERNAME/$IMAGE_NAME:mysql
    docker push $DOCKER_USERNAME/$IMAGE_NAME:mysql-$VERSION
    docker push $DOCKER_USERNAME/$IMAGE_NAME:latest

    docker push $DOCKER_USERNAME/$IMAGE_NAME:postgresql
    docker push $DOCKER_USERNAME/$IMAGE_NAME:postgresql-$VERSION

    echo ""
    echo "╔════════════════════════════════════════════════════════╗"
    echo "║  ✅ All images pushed to Docker Hub successfully!     ║"
    echo "║                                                        ║"
    echo "║  🌐 Available at:                                     ║"
    echo "║     docker pull $DOCKER_USERNAME/$IMAGE_NAME:standalone           ║"
    echo "║     docker pull $DOCKER_USERNAME/$IMAGE_NAME:sqlite               ║"
    echo "║     docker pull $DOCKER_USERNAME/$IMAGE_NAME:mysql                ║"
    echo "║     docker pull $DOCKER_USERNAME/$IMAGE_NAME:postgresql           ║"
    echo "║     docker pull $DOCKER_USERNAME/$IMAGE_NAME:latest               ║"
    echo "╚════════════════════════════════════════════════════════╝"
else
    echo "⏭️  Skipped push to Docker Hub"
fi

echo ""
echo "📚 Documentation: DOCKER_README.md"
echo "🔗 Docker Hub: https://hub.docker.com/r/$DOCKER_USERNAME/$IMAGE_NAME"
echo ""
