<div align="center">

# 🖥️ TecnoGest

### Sistema de Gestión de Inventario Tecnológico

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4.x-FFAA00?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com)

**Sistema integral para administrar computadoras, impresoras, proyectores, componentes, mantenimientos y más.**

[Comenzar](#-inicio-rápido) • [Características](#-características) • [Instalación](#-instalación) • [Documentación](#-documentación)

</div>

---

## 📑 Tabla de Contenidos

- [✨ Características](#-características)
- [🚀 Inicio Rápido](#-inicio-rápido)
- [⚙️ Instalación Detallada](#%EF%B8%8F-instalación-detallada)
- [🎯 Uso del Sistema](#-uso-del-sistema)
- [🔧 Comandos Útiles](#-comandos-útiles)
- [🐛 Solución de Problemas](#-solución-de-problemas)
- [📚 Tecnologías](#-tecnologías)

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

> **¿Primera vez?** Sigue estos pasos simples para tener el sistema funcionando.

### Prerequisitos

- ✅ **Docker Desktop** o **Docker Engine + Docker Compose**
- ✅ **Git**

### Instalación con Laravel Sail (Docker)

```bash
# 1️⃣ Clonar el proyecto
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest

# 2️⃣ Copiar archivo de entorno
cp .env.example .env

# 3️⃣ Instalar dependencias (primera vez)
docker run --rm \
    -v "$(pwd):/opt" \
    -w /opt \
    laravelsail/php84-composer:latest \
    bash -c "composer install && php artisan sail:install --with=mysql"

# 4️⃣ Iniciar contenedores
./vendor/bin/sail up -d

# 5️⃣ Configurar base de datos (solo primera vez)
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed

# 6️⃣ Compilar assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 🎉 ¡Listo!

Abre tu navegador en: **http://localhost**

**Credenciales de acceso:**
```
Email:    admin@tecnogest.com
Password: password
```

### ⚡ Crear Alias (Opcional pero Recomendado)
```bash
# Linux/Mac
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc

# Windows (PowerShell)
Set-Alias sail "./vendor/bin/sail"

# Ahora usa: sail up -d, sail artisan migrate, etc.
```

---

## ⚙️ Instalación Detallada

<details>
<summary><b>📦 Opción 1: Con Laravel Sail (Docker - Desarrollo)</b></summary>

### Paso 1: Clonar y Configurar

```bash
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest
cp .env.example .env
```

### Paso 2: Instalar Dependencias

```bash
# Instalar Composer y seleccionar MySQL
docker run --rm \
    -v "$(pwd):/opt" \
    -w /opt \
    laravelsail/php84-composer:latest \
    bash -c "composer install && php artisan sail:install --with=mysql"
```

### Paso 3: Iniciar Contenedores

```bash
./vendor/bin/sail up -d
```

💡 **Tip: Crear alias para comandos más cortos**
```bash
# Linux/Mac
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc

# Windows (Git Bash)
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc

# Ahora puedes usar: sail up -d
```

### Paso 4: Configurar Base de Datos

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

**Datos de prueba incluidos:**
- ✅ 3 Usuarios (admin, soporte, viewer)
- ✅ 10 Computadoras (5 activas, 5 en mantenimiento)
- ✅ 8 Impresoras distribuidas
- ✅ 6 Proyectores
- ✅ 218 Componentes de hardware
- ✅ 122 Repuestos para impresoras/proyectores
- ✅ 35 Ubicaciones en 7 pabellones

### Paso 5: Compilar Assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 🎉 ¡Listo! Accede en http://localhost

</details>

<details>
<summary><b>🐳 Opción 2: Docker Producción (Imagen Standalone)</b></summary>

### Prerrequisitos
- Docker Engine 20.10+
- Docker Compose 2.0+

### Paso 1: Configurar Variables

```bash
# Copiar archivo de producción
cp .env.production .env

# Editar con tus credenciales
nano .env
```

**Variables importantes a configurar:**
```bash
APP_KEY=              # Generar con: php artisan key:generate
APP_URL=http://tu-dominio.com
DB_DATABASE=tecnogest
DB_USERNAME=tecnogest_user
DB_PASSWORD=TU_PASSWORD_SEGURO
DB_ROOT_PASSWORD=TU_ROOT_PASSWORD_SEGURO
```

### Paso 2: Construir Imagen

```bash
# Construir imagen de producción
docker build -f Dockerfile.production -t tecnogest:latest .
```

### Paso 3: Iniciar con Docker Compose

```bash
# Primera vez (con migraciones y seeders)
RUN_MIGRATIONS=true RUN_SEEDERS=true \
docker-compose -f docker-compose.production.yml up -d

# Siguientes veces
docker-compose -f docker-compose.production.yml up -d
```

### Paso 4: Verificar

```bash
# Ver logs
docker-compose -f docker-compose.production.yml logs -f app

# Health check
curl http://localhost/health

# Acceder: http://localhost
```

### 🎉 ¡Sistema en producción!

**Ver [DOCKER.md](DOCKER.md) para guía completa de Docker.**

</details>

<details>
<summary><b>🔧 Opción 3: Instalación Manual (Sin Docker)</b></summary>

### Requisitos Previos

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y
```

### 1. Instalar PHP 8.4 y Extensiones

```bash
# Agregar repositorio de PHP 8.4 (Ubuntu/Debian)
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Instalar PHP 8.4 y extensiones requeridas
sudo apt install -y \
    php8.4-cli \
    php8.4-fpm \
    php8.4-common \
    php8.4-mysql \
    php8.4-zip \
    php8.4-gd \
    php8.4-mbstring \
    php8.4-curl \
    php8.4-xml \
    php8.4-bcmath \
    php8.4-intl \
    php8.4-opcache

# Verificar instalación
php -v
# Debe mostrar: PHP 8.4.x
```

### 2. Instalar Composer 2.x

```bash
# Descargar e instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verificar
composer --version
# Debe mostrar: Composer version 2.x
```

### 3. Instalar Node.js 20.x (Para Assets)

```bash
# Instalar Node.js LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verificar
node -v   # v20.x
npm -v    # 10.x
```

### 4. Instalar y Configurar MySQL

```bash
# Instalar MySQL Server
sudo apt install -y mysql-server

# Configuración segura
sudo mysql_secure_installation
# Responde: Yes a todas las preguntas

# Crear base de datos y usuario
sudo mysql -u root -p
```

```sql
-- Dentro de MySQL
CREATE DATABASE tecnogest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tecnogest_user'@'localhost' IDENTIFIED BY 'password_seguro_123';
GRANT ALL PRIVILEGES ON tecnogest.* TO 'tecnogest_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Clonar e Instalar Proyecto

```bash
# Clonar repositorio
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest

# Configurar entorno
cp .env.example .env
nano .env  # Editar credenciales de BD

# Configuración .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tecnogest
# DB_USERNAME=tecnogest_user
# DB_PASSWORD=password_seguro_123

# Instalar dependencias PHP
composer install

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones con datos demo
php artisan migrate --seed

# Instalar y compilar assets
npm install
npm run build
```

### 6. Iniciar Servidor de Desarrollo

```bash
# Opción 1: Servidor PHP integrado
php artisan serve
# Accede en: http://localhost:8000

# Opción 2: Con Nginx (producción)
# Ver DEPLOYMENT.md para configuración completa
```

</details>

---

## 🎯 Uso del Sistema

### Acceso Inicial

1. Abre tu navegador en **http://localhost**
2. Ingresa con las credenciales:
   - Email: `admin@tecnogest.com`
   - Password: `password`

### Funcionalidades Principales

<table>
<tr><td width="50%">

**📦 Gestionar Inventario**
- Ve a "Dispositivos" → "Computadoras"
- Agrega, edita o elimina equipos
- Asigna componentes a dispositivos
- Visualiza historial completo

</td><td width="50%">

**🔧 Registrar Mantenimientos**
- Ve a "Operaciones" → "Mantenimientos"
- Crea nuevos registros
- Selecciona tipo (Preventivo/Correctivo)
- Asigna técnico responsable

</td></tr>
<tr><td width="50%">

**📍 Transferir Equipos**
- Ve a "Operaciones" → "Transferencias"
- Selecciona dispositivo y destino
- Registra responsable y observaciones
- Realiza seguimiento del traslado

</td><td width="50%">

**📊 Exportar Reportes**
- Abre cualquier tabla de dispositivos
- Haz clic en "Exportar"
- Descarga en formato Excel
- Incluye historial completo

</td></tr>
</table>

---

## 🔧 Comandos Útiles

<details>
<summary><b>🐳 Gestión de Contenedores Docker</b></summary>

```bash
# Iniciar todos los servicios
./vendor/bin/sail up -d

# Detener servicios
./vendor/bin/sail down

# Ver logs en tiempo real
./vendor/bin/sail logs -f

# Reiniciar servicios
./vendor/bin/sail restart

# Ver estado de contenedores
./vendor/bin/sail ps
```

</details>

<details>
<summary><b>🗄️ Base de Datos</b></summary>

```bash
# Ejecutar migraciones
./vendor/bin/sail artisan migrate

# Resetear base de datos (¡CUIDADO! Borra todo)
./vendor/bin/sail artisan migrate:fresh --seed

# Acceder a consola MySQL
./vendor/bin/sail mysql

# Ver estadísticas de tablas
./vendor/bin/sail artisan db:show --counts

# Crear backup
./vendor/bin/sail exec mysql mysqldump -u sail -ppassword informatica > backup.sql
```

</details>

<details>
<summary><b>🎨 Desarrollo Frontend</b></summary>

```bash
# Compilar assets (producción)
./vendor/bin/sail npm run build

# Modo desarrollo con hot-reload
./vendor/bin/sail npm run dev

# Limpiar caché de vistas
./vendor/bin/sail artisan view:clear
```

</details>

<details>
<summary><b>🔍 Debugging y Mantenimiento</b></summary>

```bash
# Limpiar todas las cachés
./vendor/bin/sail artisan optimize:clear

# Acceder a Tinker (consola interactiva)
./vendor/bin/sail artisan tinker

# Acceder al shell del contenedor
./vendor/bin/sail shell

# Ejecutar tests
./vendor/bin/sail artisan test

# Ver rutas disponibles
./vendor/bin/sail artisan route:list
```

</details>

---

## 📚 Tecnologías

<div align="center">

| Categoría | Tecnología | Versión |
|-----------|-----------|---------|
| **Backend** | Laravel | 12.x |
| **Admin Panel** | Filament PHP | 4.x |
| **Lenguaje** | PHP | 8.4 |
| **Base de Datos** | MySQL / SQLite | 8.0+ / 3.x |
| **Cache** | Redis | Alpine |
| **Frontend** | Livewire | 3.x |
| **Estilos** | TailwindCSS | 4.x |
| **Build Tool** | Vite | 7.x |
| **Contenedores** | Docker / Sail | 20.10+ |

</div>

### Estructura del Proyecto

```
TecnoGest/
├── app/
│   ├── Constants/          # Clases de constantes (Status, DeviceTypes)
│   ├── Exports/            # Exportaciones a Excel
│   ├── Filament/           # Recursos del panel admin
│   │   ├── Resources/      # CRUD de dispositivos
│   │   ├── Pages/          # Páginas personalizadas
│   │   └── Widgets/        # Widgets del dashboard
│   ├── Models/             # 30+ modelos Eloquent
│   └── Providers/          # Service providers
├── database/
│   ├── migrations/         # 32 migraciones
│   └── seeders/            # 18 seeders con datos de prueba
├── resources/
│   ├── css/                # Estilos (TailwindCSS)
│   ├── js/                 # JavaScript (Alpine.js)
│   └── views/              # Vistas Blade
├── public/                 # Assets compilados
├── compose.yaml            # Configuración Docker Sail
└── .env.example            # Variables de entorno
```

---

## 📦 Referencia de Instalación Manual

<details>
<summary><b>📋 Ver comandos de instalación manual completa</b></summary>

Si prefieres instalar las dependencias localmente sin usar Docker, sigue estos pasos:

### 1️⃣ Instalar PHP 8.4 y Extensiones

```bash
sudo apt update
sudo apt install -y php8.4 php8.4-cli php8.4-fpm \
    php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip \
    php8.4-intl php8.4-dom php8.4-bcmath php8.4-gd

# Extensión para base de datos (elige según tu DB):
sudo apt install -y php8.4-mysql      # MySQL/MariaDB
sudo apt install -y php8.4-pgsql      # PostgreSQL
sudo apt install -y php8.4-sqlite3    # SQLite
```

### 2️⃣ Instalar Composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
php -r "unlink('composer-setup.php');"
```

### 3️⃣ Instalar Node.js 20 LTS

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 4️⃣ Configurar Base de Datos (Ejemplo: MySQL)

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

```sql
CREATE DATABASE informatica;
CREATE USER 'tecnogest'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON informatica.* TO 'tecnogest'@'localhost';
FLUSH PRIVILEGES;
```

### 5️⃣ Instalar Proyecto

```bash
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest
cp .env.example .env
nano .env  # Editar credenciales de BD

composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve  # http://localhost:8000
```

</details>

---

## 🚀 Despliegue en Producción

<details>
<summary><b>☁️ Servidor VPS con Nginx</b></summary>

### Preparar Servidor

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server redis-server
```

### Instalar Proyecto

```bash
cd /var/www
sudo git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest

composer install --optimize-autoloader --no-dev
cp .env.example .env
nano .env  # Configurar para producción

php artisan key:generate
php artisan migrate --force
npm install && npm run build

# Optimizaciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

### SSL con Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d tudominio.com
```

</details>

<details>
<summary><b>🔐 Checklist de Seguridad</b></summary>

```bash
# 1. Cambiar credenciales por defecto
php artisan tinker
>>> User::where('email', 'admin@tecnogest.com')->first()->update(['password' => Hash::make('NuevaPasswordSegura')]);

# 2. Configurar .env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# 3. Implementar HTTPS
sudo certbot --nginx -d tudominio.com
```

</details>

### Opción 2: Docker en Producción

```bash
# 1. Clonar repositorio y configurar
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest
cp .env.example .env

# 2. Editar .env con credenciales de producción

# 3. Construir e iniciar contenedores
docker-compose up -d

# 4. Ejecutar migraciones
docker-compose exec app php artisan migrate --force

# 5. Optimizar
docker-compose exec app php artisan optimize
```

## �� Seguridad en Producción

```bash
# 1. Cambiar credenciales por defecto
./vendor/bin/sail artisan tinker
>>> $user = User::where('email', 'admin@tecnogest.com')->first();
>>> $user->password = Hash::make('nueva_contraseña_segura');
>>> $user->save();

# 2. Desactivar debug mode en .env
APP_DEBUG=false
APP_ENV=production

# 3. Implementar HTTPS
sudo certbot --nginx -d tudominio.com
```

## 📊 Estructura del Proyecto

```
TecnoGest/
├── app/
│   ├── Constants/          # Clases de constantes
│   ├── Exports/            # Exportaciones a Excel
│   ├── Filament/           # Recursos de Filament
│   ├── Models/             # Modelos Eloquent
│   └── Providers/          # Service Providers
├── database/
│   ├── migrations/         # 31 migraciones
│   └── seeders/            # 12 seeders
├── resources/
│   ├── css/                # Estilos
│   ├── js/                 # JavaScript
│   └── views/              # Vistas Blade
├── compose.yaml           # Docker Sail
└── .env.example           # Variables de entorno
```

## 🐛 Solución de Problemas

<details>
<summary><b>❌ Error: Puerto 80 ya está en uso</b></summary>

**Síntoma:** Al ejecutar `sail up -d` aparece: `bind: address already in use`

**Causa:** Apache u otro servicio está usando el puerto 80.

**Solución:**
```bash
# Detener Apache
sudo systemctl stop apache2
sudo systemctl disable apache2

# Reiniciar Sail
./vendor/bin/sail up -d
```

</details>

<details>
<summary><b>❌ Error: "Call to undefined function mb_split()"</b></summary>

**Causa:** Falta la extensión `mbstring` de PHP.

**Solución:**
```bash
# Instalar extensión
sudo apt install -y php8.4-mbstring

# Verificar
php -m | grep mbstring

# Reinstalar dependencias
composer clear-cache
composer install
```

</details>

<details>
<summary><b>❌ Problemas con permisos</b></summary>

**Síntoma:** Errores al guardar archivos o logs.

**Solución con Docker:**
```bash
./vendor/bin/sail artisan storage:link
./vendor/bin/sail shell
chmod -R 775 storage bootstrap/cache
```

**Solución sin Docker:**
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

</details>

<details>
<summary><b>❌ Docker/Sail no inicia</b></summary>

**Solución:**
```bash
# 1. Verificar Docker
sudo systemctl status docker

# 2. Reiniciar Docker
sudo systemctl restart docker

# 3. Reconstruir contenedores
./vendor/bin/sail down -v
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

</details>

<details>
<summary><b>❌ Base de datos no conecta</b></summary>

**Verifica tu `.env`:**

Con Docker (Sail):
```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=informatica
DB_USERNAME=sail
DB_PASSWORD=password
```

Sin Docker (local):
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=informatica
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

</details>

<details>
<summary><b>❌ Página en blanco o error 500</b></summary>

**Solución:**
```bash
# Limpiar todas las cachés
./vendor/bin/sail artisan optimize:clear

# Regenerar configuración
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache

# Verificar permisos
./vendor/bin/sail artisan storage:link
```

</details>

<details>
<summary><b>💡 ¿Aún tienes problemas?</b></summary>

1. Revisa los logs: `./vendor/bin/sail logs -f`
2. Verifica el estado: `./vendor/bin/sail ps`
3. Abre un issue en: [GitHub Issues](https://github.com/Gzus-cmd/TecnoGest/issues)

</details>

---

## 🤝 Contribuir

¿Encontraste un bug? ¿Tienes una idea para mejorar? ¡Las contribuciones son bienvenidas!

1. Fork el proyecto
2. Crea tu rama de característica (`git checkout -b feature/MiCaracteristica`)
3. Commit tus cambios (`git commit -m 'Agregar MiCaracterística'`)
4. Push a la rama (`git push origin feature/MiCaracteristica`)
5. Abre un Pull Request

---

<div align="center">

## 📞 Contacto y Soporte

[![GitHub Issues](https://img.shields.io/badge/Issues-GitHub-red?style=for-the-badge&logo=github)](https://github.com/Gzus-cmd/TecnoGest/issues)
[![GitHub Discussions](https://img.shields.io/badge/Discussions-GitHub-blue?style=for-the-badge&logo=github)](https://github.com/Gzus-cmd/TecnoGest/discussions)

**Desarrollado por** [Gzus-cmd](https://github.com/Gzus-cmd)

---

### 📝 Licencia

Este proyecto está bajo la Licencia MIT - consulta el archivo [LICENSE](LICENSE) para más detalles.

---

**TecnoGest** © 2025 - Sistema de Gestión de Inventario Tecnológico

⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub

</div>
