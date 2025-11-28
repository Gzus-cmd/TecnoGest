# 🖥️ TecnoGest

Sistema integral de gestión de inventario tecnológico desarrollado con Laravel 12 y Filament 4. TecnoGest permite administrar computadoras, impresoras, proyectores, componentes de hardware, piezas de repuesto, mantenimientos, transferencias y más.

## 📋 Características Principales

- ✅ **Gestión de Dispositivos**: Computadoras, impresoras y proyectores
- ✅ **Inventario de Componentes**: CPUs, GPUs, RAM, discos duros, placas base, etc.
- ✅ **Piezas de Repuesto**: Cabezales, rodillos, fusores, lámparas, lentes, filtros
- ✅ **Sistema de Componentes**: Asignación de componentes a dispositivos con historial
- ✅ **Mantenimientos**: Registro de mantenimientos de rutina y correctivos
- ✅ **Transferencias**: Control de movimientos de equipos entre ubicaciones
- ✅ **Proveedores**: Gestión de proveedores de equipos y repuestos
- ✅ **Ubicaciones**: Organización por pabellones y ambientes
- ✅ **Exportación de Datos**: Historial de componentes a Excel
- ✅ **Interfaz Moderna**: Desarrollada con Filament PHP

## 🛠️ Tecnologías Utilizadas

- **Framework**: Laravel 12.x
- **Panel de Administración**: Filament 4.x
- **Base de Datos**: MySQL 8.0
- **Cache**: Redis
- **Frontend**: Livewire 3.x, Alpine.js, Tailwind CSS
- **Contenedores**: Docker (Laravel Sail)
- **PHP**: 8.4

## 💻 Requerimientos Mínimos

### Hardware
- **Procesador**: Dual Core 2.0 GHz o superior
- **Memoria RAM**: 4 GB mínimo (8 GB recomendado)
- **Almacenamiento**: 2 GB de espacio libre
- **Red**: Conexión a Internet (para instalación inicial)

### Software
- **Sistema Operativo**: 
  - Linux (Ubuntu 20.04+, Debian 11+)
  - macOS (10.15+)
  - Windows 10/11 (con WSL2)
- **Docker**: 20.10+ y Docker Compose 2.0+
- **Git**: 2.30+
- **Navegador Web**: Chrome, Firefox, Edge o Safari (última versión)

## 🚀 Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest
```

### 2. Configurar Variables de Entorno

```bash
# Copiar el archivo de ejemplo
cp .env.example .env
```

Edita el archivo `.env` y configura las siguientes variables (valores por defecto para Sail):

```env
APP_NAME=TecnoGest
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql_tecnogest
DB_PORT=3306
DB_DATABASE=informatica
DB_USERNAME=sail
DB_PASSWORD=password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis_tecnogest
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Instalar Dependencias de PHP

```bash
# Si tienes Composer instalado localmente
composer install

# O usando Docker (sin necesidad de Composer local)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 4. Iniciar Laravel Sail

```bash
# Dar permisos de ejecución al script de Sail
chmod +x vendor/bin/sail

# Iniciar los contenedores en segundo plano
./vendor/bin/sail up -d
```

**Alias recomendado** (opcional pero muy útil):
```bash
# Agregar a tu ~/.bashrc o ~/.zshrc
alias sail='./vendor/bin/sail'

# Recargar configuración
source ~/.bashrc  # o source ~/.zshrc
```

Ahora puedes usar `sail` en lugar de `./vendor/bin/sail`:
```bash
sail up -d
sail artisan migrate
sail down
```

### 5. Generar Clave de Aplicación

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Ejecutar Migraciones y Seeders

```bash
# Crear las tablas y poblar con datos de prueba
./vendor/bin/sail artisan migrate:fresh --seed
```

Esto creará:
- 2 usuarios de prueba (admin@tecnogest.com y soporte@tecnogest.com)
- 35 ubicaciones en 6 pabellones
- 7 proveedores
- 218 componentes de hardware
- 25 modelos de impresoras
- 25 modelos de proyectores
- 54 piezas de repuesto

### 7. Instalar Dependencias de Node.js y Compilar Assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 8. Acceder a la Aplicación

Abre tu navegador y visita: **http://localhost**

**Credenciales de acceso:**
- **Email**: `admin@tecnogest.com`
- **Password**: `password`

O también:
- **Email**: `soporte@tecnogest.com`
- **Password**: `password`

## 🔧 Comandos Útiles de Desarrollo

### Gestión de Contenedores

```bash
# Iniciar contenedores
./vendor/bin/sail up

# Iniciar en segundo plano
./vendor/bin/sail up -d

# Detener contenedores
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs

# Ver logs en tiempo real
./vendor/bin/sail logs -f
```

### Base de Datos

```bash
# Ejecutar migraciones
./vendor/bin/sail artisan migrate

# Revertir última migración
./vendor/bin/sail artisan migrate:rollback

# Recrear base de datos desde cero
./vendor/bin/sail artisan migrate:fresh

# Recrear con datos de prueba
./vendor/bin/sail artisan migrate:fresh --seed

# Acceder a MySQL
./vendor/bin/sail mysql

# Ver estadísticas de la base de datos
./vendor/bin/sail artisan db:show --counts
```

### Desarrollo

```bash
# Compilar assets en modo desarrollo
./vendor/bin/sail npm run dev

# Limpiar cachés
./vendor/bin/sail artisan optimize:clear

# Ejecutar tests
./vendor/bin/sail artisan test
```

### Acceso a Servicios

```bash
# Shell de PHP/Laravel
./vendor/bin/sail shell

# Shell de MySQL
./vendor/bin/sail mysql

# Shell de Redis
./vendor/bin/sail redis

# Tinker (REPL de Laravel)
./vendor/bin/sail artisan tinker
```

## 📦 Despliegue en Producción

### Opción 1: Servidor Tradicional (VPS)

#### Requisitos del Servidor
- Ubuntu 22.04 LTS / Debian 12
- PHP 8.4 con extensiones: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- MySQL 8.0 o PostgreSQL 15+
- Nginx o Apache
- Composer 2.x
- Node.js 20+ y NPM
- Redis (opcional pero recomendado)

#### Pasos de Instalación

```bash
# 1. Clonar repositorio
git clone https://github.com/Gzus-cmd/TecnoGest.git
cd TecnoGest

# 2. Instalar dependencias
composer install --optimize-autoloader --no-dev

# 3. Configurar .env para producción
cp .env.example .env
nano .env

# 4. Generar clave
php artisan key:generate

# 5. Ejecutar migraciones
php artisan migrate --force

# 6. Compilar assets
npm install
npm run build

# 7. Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Configurar permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 9. Crear enlace simbólico de storage
php artisan storage:link
```

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

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Proyecto de código abierto bajo licencia MIT.

## 👨‍💻 Autor

**Gzus-cmd** - [@Gzus-cmd](https://github.com/Gzus-cmd)

## 📞 Soporte

- [Issues](https://github.com/Gzus-cmd/TecnoGest/issues)
- Contacta al equipo de desarrollo

---

**TecnoGest** - Sistema de Gestión de Inventario Tecnológico © 2025
