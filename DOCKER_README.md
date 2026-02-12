# 🐳 TecnoGest - Docker Deployment Guide

Sistema de Gestión de Inventario TecnoGest con soporte para múltiples bases de datos.

## 📦 Versiones Disponibles

TecnoGest está disponible en **4 variantes** en Docker Hub:

| Tag | Base de Datos | Uso Recomendado | Comando |
|-----|---------------|-----------------|---------|
| `standalone` | SQLite (embebida) | Desarrollo, pruebas rápidas | `docker-compose -f docker-compose.standalone.yml up -d` |
| `sqlite` | SQLite con persistencia | Proyectos pequeños, demos | `docker-compose -f docker-compose.sqlite.yml up -d` |
| `mysql` | MySQL 8.0 | Producción (recomendado) | `docker-compose -f docker-compose.mysql.yml up -d` |
| `postgresql` | PostgreSQL 16 | Producción alternativa | `docker-compose -f docker-compose.postgresql.yml up -d` |

## 🚀 Inicio Rápido

### Opción 1: MySQL (Recomendado para Producción)

```bash
# 1. Descargar docker-compose
curl -O https://raw.githubusercontent.com/Gzus-cmd/TecnoGest/deploy/docker-compose.mysql.yml

# 2. Crear archivo .env (opcional - tiene valores por defecto)
cat > .env << EOF
DB_DATABASE=tecnogest
DB_USERNAME=tecnogest
DB_PASSWORD=tu_password_seguro
DB_ROOT_PASSWORD=root_password_seguro
APP_KEY=base64:$(openssl rand -base64 32)
EOF

# 3. Iniciar contenedores
docker-compose -f docker-compose.mysql.yml up -d

# 4. Ejecutar migraciones (primera vez)
docker exec tecnogest-app php artisan migrate --seed

# 5. Acceder a la aplicación
# URL: http://localhost:8080/admin
# Usuario: admin@tecnogest.com
# Password: password
```

### Opción 2: SQLite (Sin Base de Datos Externa)

```bash
# Comando único - listo para usar
docker-compose -f docker-compose.sqlite.yml up -d
docker exec tecnogest-sqlite php artisan migrate --seed
```

### Opción 3: PostgreSQL

```bash
docker-compose -f docker-compose.postgresql.yml up -d
docker exec tecnogest-app php artisan migrate --seed
```

### Opción 4: Standalone (SQLite + Auto-configuración)

```bash
# La más simple - ideal para pruebas
docker-compose -f docker-compose.standalone.yml up -d
docker exec tecnogest-standalone php artisan migrate --seed
```

## ⚙️ Configuración Avanzada

### Variables de Entorno

Todas las variantes soportan estas variables en el archivo `.env`:

```env
# Aplicación
APP_NAME="TecnoGest"
APP_ENV=production          # production, local, staging
APP_DEBUG=false             # true solo para desarrollo
APP_URL=http://localhost:8080

# Seguridad (CAMBIAR EN PRODUCCIÓN)
APP_KEY=base64:YOUR_KEY_HERE   # Generar con: php artisan key:generate --show

# Base de Datos (según variante)
# MySQL
DB_DATABASE=tecnogest
DB_USERNAME=tecnogest
DB_PASSWORD=tecnogest2024
DB_ROOT_PASSWORD=root2024

# PostgreSQL
DB_DATABASE=tecnogest
DB_USERNAME=tecnogest
DB_PASSWORD=tecnogest2024

# Automatización (opcional)
AUTO_MIGRATE=true           # Ejecutar migraciones al iniciar
AUTO_SEED=true              # Ejecutar seeders al iniciar
SEEDER_CLASS=DemoSeeder     # Clase del seeder a ejecutar
```

### Puertos Personalizados

Cambiar el puerto 8080 por defecto:

```bash
# Editar docker-compose.*.yml
ports:
  - "3000:80"  # Cambiar 8080 a 3000
```

### Persistencia de Datos

Los datos se guardan en volúmenes Docker:

```bash
# Ver volúmenes
docker volume ls | grep tecnogest

# Backup de base de datos MySQL
docker exec tecnogest-mysql mysqldump -u root -proot2024 tecnogest > backup.sql

# Backup de base de datos PostgreSQL
docker exec tecnogest-postgres pg_dump -U tecnogest tecnogest > backup.sql

# Backup de SQLite
docker cp tecnogest-sqlite:/var/www/html/database/tecnogest.sqlite ./backup.sqlite
```

## 🔧 Gestión de Contenedores

### Comandos Útiles

```bash
# Ver logs
docker-compose -f docker-compose.mysql.yml logs -f

# Logs solo de la app
docker logs tecnogest-app -f

# Ejecutar comandos artisan
docker exec tecnogest-app php artisan list
docker exec tecnogest-app php artisan migrate
docker exec tecnogest-app php artisan db:seed
docker exec tecnogest-app php artisan cache:clear

# Entrar al contenedor
docker exec -it tecnogest-app sh

# Reiniciar servicios
docker-compose -f docker-compose.mysql.yml restart

# Detener todo
docker-compose -f docker-compose.mysql.yml down

# Detener y eliminar volúmenes (CUIDADO: borra datos)
docker-compose -f docker-compose.mysql.yml down -v
```

### Health Checks

Verificar que todo funciona:

```bash
# Estado de contenedores
docker-compose -f docker-compose.mysql.yml ps

# Verificar salud de la app
curl http://localhost:8080/up

# Verificar base de datos
docker exec tecnogest-app php artisan db:monitor
```

## 🏗️ Construcción de Imágenes

### Construir tu propia imagen

```bash
# Construir imagen base
docker build -t gzus07/tecnogest:latest .

# Construir variante específica
docker build -t gzus07/tecnogest:mysql .
docker build -t gzus07/tecnogest:postgresql .
docker build -t gzus07/tecnogest:sqlite .
```

### Push a Docker Hub

```bash
# Login
docker login

# Tag y push
docker tag gzus07/tecnogest:latest gzus07/tecnogest:mysql
docker push gzus07/tecnogest:mysql

docker tag gzus07/tecnogest:latest gzus07/tecnogest:postgresql
docker push gzus07/tecnogest:postgresql

docker tag gzus07/tecnogest:latest gzus07/tecnogest:sqlite
docker push gzus07/tecnogest:sqlite

docker tag gzus07/tecnogest:latest gzus07/tecnogest:standalone
docker push gzus07/tecnogest:standalone
```

## 📊 Rendimiento y Optimización

### Optimizaciones Incluidas

✅ **Multi-stage build**: Imagen final ~150MB (vs ~800MB sin optimizar)
✅ **OPcache habilitado**: Cache de bytecode PHP  
✅ **JIT enabled**: Compilación Just-In-Time para PHP 8.4  
✅ **Nginx optimizado**: Compresión gzip, cache de assets  
✅ **Composer optimizado**: Autoloader classmap authoritative  
✅ **Assets pre-compilados**: Vite build en imagen  
✅ **Índices de BD**: 14 índices optimizados para queries  

### Configuración de Recursos

Para entornos de producción, ajusta los límites:

```yaml
# En docker-compose.*.yml
services:
  tecnogest:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 1G
        reservations:
          cpus: '0.5'
          memory: 512M
```

## 🔐 Seguridad

### Checklist de Producción

- [ ] Cambiar `APP_KEY` (único por instalación)
- [ ] Cambiar contraseñas de base de datos
- [ ] Cambiar credenciales por defecto (<admin@tecnogest.com>)
- [ ] Configurar `APP_DEBUG=false`
- [ ] Configurar `APP_ENV=production`
- [ ] Usar HTTPS (reverse proxy recomendado)
- [ ] Configurar firewall para puertos
- [ ] Hacer backups regulares

### Reverse Proxy (Nginx/Traefik)

Ejemplo con Nginx:

```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    
    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## 🐛 Troubleshooting

### Problemas Comunes

**Error: "Connection refused" al iniciar**

```bash
# La base de datos tarda en iniciar, espera 30s y reinicia
docker-compose -f docker-compose.mysql.yml restart tecnogest
```

**Error: "SQLSTATE[HY000] [2002] Connection refused"**

```bash
# Verificar que la BD está corriendo
docker-compose -f docker-compose.mysql.yml ps
docker logs tecnogest-mysql

# Reintentar conexión
docker exec tecnogest-app php artisan db:monitor
```

**Error: "APP_KEY not set"**

```bash
# Generar nueva key
docker exec tecnogest-app php artisan key:generate --force
```

**Error: "Storage link not found"**

```bash
# Recrear link de storage
docker exec tecnogest-app php artisan storage:link
```

**Permisos de archivos**

```bash
# Dentro del contenedor
docker exec tecnogest-app sh -c "chown -R www:www storage bootstrap/cache && chmod -R 775 storage bootstrap/cache"
```

## 📈 Monitoreo

### Logs

```bash
# Logs de aplicación Laravel
docker exec tecnogest-app tail -f storage/logs/laravel.log

# Logs de PHP
docker exec tecnogest-app tail -f /var/log/php/php-errors.log

# Logs de Nginx
docker exec tecnogest-app tail -f /var/log/nginx/access.log
docker exec tecnogest-app tail -f /var/log/nginx/error.log
```

### Métricas

```bash
# Stats de contenedor
docker stats tecnogest-app

# Espacio usado por volúmenes
docker system df -v | grep tecnogest
```

## 🆘 Soporte

- **Documentación**: [README.md](../README.md)
- **Issues**: [GitHub Issues](https://github.com/Gzus-cmd/TecnoGest/issues)
- **Docker Hub**: [gzus07/tecnogest](https://hub.docker.com/r/gzus07/tecnogest)

## 📝 Licencia

Proyecto TecnoGest - Sistema de Gestión de Inventario

---

**Versión Docker**: 2.0  
**Última actualización**: Enero 2026
