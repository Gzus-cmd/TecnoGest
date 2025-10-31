# TecnoGest

## Descripción
Proyecto Laravel con Filament para gestión tecnológica.

## Requisitos Previos
- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js & NPM

## Instalación y Despliegue

### 1. Clonar el repositorio
```bash
git clone <repository-url>
cd TecnoGest
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Configurar el archivo de entorno
```bash
cp .env.example .env
```
Edita el archivo `.env` con tus credenciales de base de datos.

### 4. Generar clave de aplicación
```bash
php artisan key:generate
```

### 5. Ejecutar migraciones
```bash
php artisan migrate
```

### 6. Instalar dependencias de Node.js y compilar assets
```bash
npm install
npm run build
```

### 7. Crear usuario administrador de Filament
```bash
php artisan make:filament-user
```

### 8. Iniciar el servidor
```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## Producción

### Optimizar para producción
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### Permisos
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```
