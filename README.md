<div align="center">

# 🖥️ TecnoGest

### Sistema de Gestión de Inventario Tecnológico

[![Version](https://img.shields.io/badge/Versión-1.0.0-success?style=for-the-badge)](https://github.com/Gzus-cmd/TecnoGest/releases)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4.x-FFAA00?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com)

**Rama `docker` — Despliegue con Docker multi-variante**

[Inicio Rápido](#-inicio-rápido) • [Variantes](#-variantes-disponibles) • [Guía Completa](DOCKER_README.md)

</div>

---

> **Nota:** Esta rama contiene la configuración Docker optimizada para self-hosting. Para desarrollo local, usa la rama [`main`](https://github.com/Gzus-cmd/TecnoGest/tree/main). Para Railway, usa la rama [`deploy`](https://github.com/Gzus-cmd/TecnoGest/tree/deploy).

---

## 📑 Tabla de Contenidos

- [🚀 Inicio Rápido](#-inicio-rápido)
- [📦 Variantes Disponibles](#-variantes-disponibles)
- [⚙️ Configuración](#%EF%B8%8F-configuración)
- [🔧 Administración](#-administración)
- [🛡️ Seguridad](#%EF%B8%8F-seguridad)
- [📚 Documentación Completa](DOCKER_README.md)

---

## 🚀 Inicio Rápido

### Desde Docker Hub (Sin compilar)

[![Docker Hub](https://img.shields.io/badge/Docker%20Hub-gzus07%2Ftecnogest-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://hub.docker.com/r/gzus07/tecnogest)

La forma más rápida — no necesitas clonar el repositorio:

```bash
# 1. Ejecutar directamente desde Docker Hub
docker run -d \
    --name tecnogest \
    -p 8080:80 \
    -e APP_KEY=base64:$(openssl rand -base64 32) \
    -e DB_CONNECTION=sqlite \
    -e DB_DATABASE=/var/www/html/database/tecnogest.sqlite \
    -e AUTO_MIGRATE=true \
    -e AUTO_SEED=true \
    gzus07/tecnogest:latest

# 2. Acceder
# URL: http://localhost:8080
# Email: admin@tecnogest.com
# Password: password
```

> **Tags disponibles:** `latest`, `1.0.0` — [ver en Docker Hub](https://hub.docker.com/r/gzus07/tecnogest/tags)

<details>
<summary><b>🐳 Docker Hub + MySQL (Producción)</b></summary>

Crea un archivo `docker-compose.yml` en cualquier carpeta:

```yaml
services:
  app:
    image: gzus07/tecnogest:latest
    container_name: tecnogest-app
    restart: unless-stopped
    ports:
      - "8080:80"
    environment:
      APP_KEY: ${APP_KEY:-base64:GENERA_UNA_KEY}
      APP_ENV: production
      APP_DEBUG: "false"
      DB_CONNECTION: mysql
      DB_HOST: mysql
      DB_PORT: 3306
      DB_DATABASE: tecnogest
      DB_USERNAME: tecnogest
      DB_PASSWORD: ${DB_PASSWORD:-tecnogest2024}
      AUTO_MIGRATE: "true"
      AUTO_SEED: "true"
      SEEDER_CLASS: DemoSeeder
    depends_on:
      mysql:
        condition: service_healthy

  mysql:
    image: mysql:8.0
    container_name: tecnogest-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: tecnogest
      MYSQL_USER: tecnogest
      MYSQL_PASSWORD: ${DB_PASSWORD:-tecnogest2024}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-root2024}
    volumes:
      - mysql_data:/var/lib/mysql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

volumes:
  mysql_data:
```

```bash
docker compose up -d
```

</details>

---

### Desde el Repositorio (Construir imagen)

### Con MySQL (Recomendado)

```bash
# 1. Clonar rama docker
git clone -b docker https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest

# 2. Iniciar contenedores (construye la imagen)
docker compose -f docker-compose.mysql.yml up -d --build

# 3. Ejecutar migraciones (primera vez)
docker exec tecnogest-app php artisan migrate --seed

# 4. Acceder
# URL: http://localhost:8080
# Email: admin@tecnogest.com
# Password: password
```

### Con SQLite (Sin BD externa)

```bash
docker compose -f docker-compose.sqlite.yml up -d --build
docker exec tecnogest-sqlite php artisan migrate --seed
# URL: http://localhost:8080
```

### Standalone (Todo incluido)

```bash
docker compose -f docker-compose.standalone.yml up -d --build
docker exec tecnogest-standalone php artisan migrate --seed
# URL: http://localhost:8080
```

---

## 📦 Variantes Disponibles

| Variante | Base de Datos | Uso Recomendado | Archivo |
|----------|---------------|-----------------|---------|
| **MySQL** | MySQL 8.0 | Producción | `docker-compose.mysql.yml` |
| **PostgreSQL** | PostgreSQL 16 | Producción alternativa | `docker-compose.postgresql.yml` |
| **SQLite** | SQLite con persistencia | Proyectos pequeños | `docker-compose.sqlite.yml` |
| **Standalone** | SQLite embebida | Demos, pruebas rápidas | `docker-compose.standalone.yml` |
| **Producción** | MySQL + optimizado | Deploy en servidor | `docker-compose.production.yml` |

---

## ⚙️ Configuración

### Variables de Entorno

Crea un archivo `.env` en la raíz del proyecto (opcional, hay valores por defecto):

```env
# Aplicación
APP_KEY=base64:GENERA_CON_php_artisan_key_generate
APP_URL=http://localhost:8080

# Base de datos (MySQL/PostgreSQL)
DB_DATABASE=tecnogest
DB_USERNAME=tecnogest
DB_PASSWORD=tu_password_seguro
DB_ROOT_PASSWORD=root_password_seguro

# Automatización al iniciar
AUTO_MIGRATE=true
AUTO_SEED=false
SEEDER_CLASS=DemoSeeder
```

### Producción

```bash
# 1. Configurar .env con credenciales seguras
cp .env.example .env
nano .env

# 2. Construir e iniciar
docker compose -f docker-compose.production.yml up -d --build

# 3. Configuración inicial
docker exec tecnogest-app php artisan migrate --force
docker exec tecnogest-app php artisan db:seed --class=ProductionSeeder --force
```

---

## 🔧 Administración

```bash
# Ver logs
docker compose -f docker-compose.mysql.yml logs -f

# Reiniciar
docker compose -f docker-compose.mysql.yml restart

# Construir imagen nueva
docker compose -f docker-compose.mysql.yml up -d --build

# Entrar al contenedor
docker exec -it tecnogest-app sh

# Backup MySQL
docker exec tecnogest-mysql mysqldump -u root -p tecnogest > backup.sql

# Restaurar
docker exec -i tecnogest-mysql mysql -u root -p tecnogest < backup.sql
```

---

## 🛡️ Seguridad

> **Importante:** Antes de exponer a internet:

1. **Cambia las contraseñas por defecto** en `.env`
2. **Genera un APP_KEY único:** `docker exec tecnogest-app php artisan key:generate --force`
3. **Cambia la contraseña del admin:**

   ```bash
   docker exec -it tecnogest-app php artisan tinker
   >>> User::where('email', 'admin@tecnogest.com')->first()->update(['password' => Hash::make('PasswordSegura')]);
   ```

4. **Configura un reverse proxy** (Nginx/Traefik) con SSL
5. **Revisa los puertos expuestos** en el compose file

---

## 🏗️ Arquitectura Docker

```
┌──────────────────────────────────────────┐
│            Docker Container              │
│  ┌──────────┐  ┌───────────────────────┐ │
│  │  Nginx   │──│      PHP-FPM 8.4      │ │
│  │  :80     │  │  Laravel 12 + Filament│ │
│  └──────────┘  └───────────────────────┘ │
│       ↑ Supervisor gestiona ambos        │
└──────────────────┬───────────────────────┘
                   │
    ┌──────────────┴──────────────┐
    │     MySQL / PostgreSQL      │
    │     (contenedor separado)   │
    └─────────────────────────────┘
```

---

## 📚 Documentación Completa

Para guía detallada de todas las variantes, configuraciones avanzadas, reverse proxy, monitoreo y más:

➡️ **[DOCKER_README.md](DOCKER_README.md)**

---

## 🌿 Otras Ramas

| Rama | Propósito |
|------|-----------|
| [`main`](https://github.com/Gzus-cmd/TecnoGest/tree/main) | Desarrollo local (sin Docker de producción) |
| `docker` (esta) | Despliegue Docker multi-variante |
| [`deploy`](https://github.com/Gzus-cmd/TecnoGest/tree/deploy) | Despliegue optimizado para Railway |

---

<div align="center">

**TecnoGest** © 2025 - Sistema de Gestión de Inventario Tecnológico

Desarrollado por [Gzus-cmd](https://github.com/Gzus-cmd)

⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub
