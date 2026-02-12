<div align="center">

# 🖥️ TecnoGest

### Sistema de Gestión de Inventario Tecnológico

[![Version](https://img.shields.io/badge/Versión-1.0.0-success?style=for-the-badge)](https://github.com/Gzus-cmd/TecnoGest/releases)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4.x-FFAA00?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com)

**Sistema integral para administrar computadoras, impresoras, proyectores, componentes, mantenimientos y más.**

[Inicio Rápido](#-inicio-rápido) • [Características](#-características) • [Instalación](#-instalación) • [Despliegue](#-despliegue)

</div>

---

## ✨ Características

<table>
<tr>
<td width="50%">

### 🖥️ Gestión de Dispositivos

- Computadoras con componentes
- Impresoras y modelos
- Proyectores
- Periféricos completos

</td>
<td width="50%">

### 📦 Inventario

- CPUs, GPUs, RAM, ROM
- Placas base y periféricos
- Repuestos (cabezales, lámparas)
- Historial de asignaciones

</td>
</tr>
<tr>
<td width="50%">

### 🔧 Mantenimiento

- Preventivo y correctivo
- Registro de técnicos
- Control de taller
- Seguimiento de estados

</td>
<td width="50%">

### 📊 Reportes y Más

- Exportación a Excel
- Transferencias entre ubicaciones
- Gestión de proveedores
- Organización por pabellones

</td>
</tr>
</table>

---

## 🚀 Inicio Rápido

> **¿Primera vez?** Sigue estos pasos para tener el sistema funcionando en minutos.

### Opción A: Con Laravel Sail (Docker)

**Prerequisitos:** Docker Desktop o Docker Engine + Docker Compose, Git

```bash
# 1. Clonar el proyecto
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest

# 2. Copiar archivo de entorno
cp .env.example .env

# 3. Instalar dependencias con contenedor temporal
docker run --rm \
    -v "$(pwd):/opt" \
    -w /opt \
    laravelsail/php84-composer:latest \
    bash -c "composer install && php artisan sail:install --with=mysql"

# 4. Iniciar contenedores
./vendor/bin/sail up -d

# 5. Configurar base de datos
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed

# 6. Compilar assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

**Accede en:** <http://localhost>

### Opción B: Instalación Manual (Sin Docker)

**Prerequisitos:** PHP 8.4+, Composer 2.x, Node.js 20+, MySQL 8.0+ o SQLite

```bash
# 1. Clonar e instalar
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest
cp .env.example .env

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar
php artisan key:generate
# Editar .env con tus credenciales de BD si usas MySQL
# Por defecto usa SQLite (no requiere configuración)

# 4. Base de datos y assets
php artisan migrate --seed
npm run build

# 5. Iniciar servidor
php artisan serve
```

**Accede en:** <http://localhost:8000>

### Credenciales de Acceso

```
Email:    admin@tecnogest.com
Password: password
```

---

## ⚙️ Instalación Detallada

<details>
<summary><b>🐧 Instalar PHP 8.4 y Extensiones (Ubuntu/Debian)</b></summary>

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y \
    php8.4-cli php8.4-fpm php8.4-common \
    php8.4-mysql php8.4-sqlite3 \
    php8.4-zip php8.4-gd php8.4-mbstring \
    php8.4-curl php8.4-xml php8.4-bcmath php8.4-intl
```

</details>

<details>
<summary><b>📦 Instalar Composer</b></summary>

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

</details>

<details>
<summary><b>🟢 Instalar Node.js 20</b></summary>

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

</details>

<details>
<summary><b>🗄️ Configurar MySQL (opcional, SQLite por defecto)</b></summary>

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

```sql
CREATE DATABASE tecnogest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tecnogest'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON tecnogest.* TO 'tecnogest'@'localhost';
FLUSH PRIVILEGES;
```

Editar `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tecnogest
DB_USERNAME=tecnogest
DB_PASSWORD=password_seguro
```

</details>

---

## 🔧 Comandos Útiles

### Desarrollo

```bash
# Modo desarrollo con hot-reload (servidor + vite + queue)
composer dev

# Setup completo desde cero
composer setup:dev

# Resetear base de datos
composer fresh

# Limpiar cachés
composer clear
```

### Laravel Sail (Docker)

```bash
# Crear alias (recomendado)
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc && source ~/.bashrc

# Gestión de contenedores
sail up -d          # Iniciar
sail down           # Detener
sail restart        # Reiniciar
sail logs -f        # Ver logs

# Artisan
sail artisan migrate
sail artisan tinker
sail artisan test

# Base de datos
sail mysql          # Consola MySQL
sail artisan migrate:fresh --seed   # Resetear BD
```

### Sin Docker

```bash
php artisan serve           # Iniciar servidor
php artisan migrate         # Migraciones
php artisan db:seed         # Datos de prueba
php artisan test            # Tests
php artisan optimize:clear  # Limpiar cachés
npm run dev                 # Vite dev server
npm run build               # Compilar assets
```

---

## 🎯 Uso del Sistema

### Funcionalidades Principales

| Módulo | Acceso | Descripción |
|--------|--------|-------------|
| **Computadoras** | Dispositivos → Computadoras | Gestión completa con componentes |
| **Impresoras** | Dispositivos → Impresoras | Modelos, repuestos, cabezales |
| **Proyectores** | Dispositivos → Proyectores | Lámparas, mantenimiento |
| **Periféricos** | Dispositivos → Periféricos | Teclados, ratones, monitores, etc. |
| **Mantenimientos** | Operaciones → Mantenimientos | Preventivo y correctivo |
| **Transferencias** | Operaciones → Transferencias | Movimiento entre ubicaciones |
| **Componentes** | Inventario → Componentes | CPU, GPU, RAM, ROM, etc. |
| **Exportar** | Botón en cada tabla | Reportes Excel completos |
| **Backup** | Administración → Backup | Respaldo de base de datos |

### Datos de Prueba Incluidos

- 3 Usuarios (admin, soporte, viewer)
- 10 Computadoras con componentes
- 8 Impresoras distribuidas
- 6 Proyectores
- 218+ Componentes de hardware
- 122+ Repuestos
- 35 Ubicaciones en 7 pabellones

---

## 🚀 Despliegue

Para desplegar en producción, usa las ramas especializadas:

| Método | Rama | Instrucciones |
|--------|------|---------------|
| **Docker Hub** | — | Imagen pre-construida lista para usar |
| **Railway** | [`deploy`](https://github.com/Gzus-cmd/TecnoGest/tree/deploy) | Deploy automático con Nixpacks |
| **Docker (build)** | [`docker`](https://github.com/Gzus-cmd/TecnoGest/tree/docker) | Construir imagen + MySQL, PostgreSQL, SQLite |
| **VPS/Manual** | `main` (esta) | Instalar con Nginx + PHP-FPM |

### Deploy con Docker Hub (Recomendado - Sin compilar)

[![Docker Hub](https://img.shields.io/badge/Docker%20Hub-gzus07%2Ftecnogest-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://hub.docker.com/r/gzus07/tecnogest)

La imagen pre-construida está disponible en Docker Hub — no necesitas clonar el repositorio ni compilar nada.

```bash
# 1. Descargar y ejecutar directamente
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

<details>
<summary><b>🐳 Docker Hub con MySQL (Producción)</b></summary>

Crea un archivo `docker-compose.yml`:

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
# URL: http://localhost:8080
```

</details>

> **Tags disponibles:** `latest`, `1.0.0`  
> **Docker Hub:** [hub.docker.com/r/gzus07/tecnogest](https://hub.docker.com/r/gzus07/tecnogest)

### Deploy rápido con Sail (desde main)

```bash
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest && cp .env.example .env
docker run --rm -v "$(pwd):/opt" -w /opt \
    laravelsail/php84-composer:latest \
    bash -c "composer install && php artisan sail:install --with=mysql"
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
```

---

## 📚 Tecnologías

| Categoría | Tecnología | Versión |
|-----------|-----------|---------|
| **Backend** | Laravel | 12.x |
| **Admin Panel** | Filament PHP | 4.x |
| **Lenguaje** | PHP | 8.4 |
| **Base de Datos** | MySQL / SQLite | 8.0+ / 3.x |
| **Frontend** | Livewire + TailwindCSS | 3.x / 4.x |
| **Build Tool** | Vite | 7.x |
| **Contenedores** | Docker / Sail | 20.10+ |

### Estructura del Proyecto

```
TecnoGest/
├── app/
│   ├── Console/Commands/   # Comandos artisan personalizados
│   ├── Constants/           # Constantes (Status, DeviceTypes)
│   ├── Exports/             # Exportaciones a Excel
│   ├── Filament/            # Panel administrativo
│   │   ├── Resources/       # CRUD de dispositivos
│   │   ├── Pages/           # Dashboard, Backup, Perfil
│   │   └── Widgets/         # Gráficos y estadísticas
│   ├── Http/Middleware/     # CacheAssets, CacheUserPermissions
│   ├── Models/              # 25+ modelos Eloquent
│   ├── Policies/            # Políticas de autorización
│   └── Providers/           # Service providers
├── database/
│   ├── migrations/          # Migraciones consolidadas
│   └── seeders/             # 18+ seeders con datos de prueba
├── resources/views/         # Vistas Blade
├── compose.yaml             # Docker Sail (desarrollo)
└── .env.example             # Variables de entorno
```

---

## 🐛 Solución de Problemas

<details>
<summary><b>Puerto 80 ya en uso</b></summary>

```bash
sudo systemctl stop apache2 && sudo systemctl disable apache2
./vendor/bin/sail up -d
```

</details>

<details>
<summary><b>Problemas de permisos</b></summary>

```bash
# Con Docker
./vendor/bin/sail shell
chmod -R 775 storage bootstrap/cache

# Sin Docker
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

</details>

<details>
<summary><b>Página en blanco o error 500</b></summary>

```bash
php artisan optimize:clear
php artisan config:cache
php artisan storage:link
```

</details>

<details>
<summary><b>Base de datos no conecta</b></summary>

Con Docker (Sail) usa `DB_HOST=mysql`. Sin Docker usa `DB_HOST=127.0.0.1`.

</details>

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crea tu rama (`git checkout -b feature/MiCaracteristica`)
3. Commit tus cambios (`git commit -m 'feat: agregar MiCaracterística'`)
4. Push a la rama (`git push origin feature/MiCaracteristica`)
5. Abre un Pull Request

---

<div align="center">

**TecnoGest** © 2025 - Sistema de Gestión de Inventario Tecnológico

Desarrollado por [Gzus-cmd](https://github.com/Gzus-cmd)

⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub

</div>
