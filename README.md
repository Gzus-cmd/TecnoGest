<div align="center">

# 🖥️ TecnoGest

### Sistema de Gestión de Inventario Tecnológico

[![Version](https://img.shields.io/badge/Versión-1.0.0-success?style=for-the-badge)](https://github.com/Gzus-cmd/TecnoGest/releases)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4.x-FFAA00?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Railway](https://img.shields.io/badge/Railway-Deploy-0B0D0E?style=for-the-badge&logo=railway&logoColor=white)](https://railway.app)

**Rama `deploy` — Optimizada para despliegue en Railway**

[Desplegar en Railway](#-despliegue-en-railway) • [Variables de Entorno](#-variables-de-entorno) • [Optimizaciones](#-optimizaciones)

</div>

---

> **Nota:** Esta rama contiene optimizaciones específicas para Railway (Nixpacks). Para desarrollo local, usa la rama [`main`](https://github.com/Gzus-cmd/TecnoGest/tree/main). Para despliegue con Docker self-hosted, usa la rama [`docker`](https://github.com/Gzus-cmd/TecnoGest/tree/docker).

---

## 📑 Tabla de Contenidos

- [🚀 Despliegue en Railway](#-despliegue-en-railway)
- [⚙️ Variables de Entorno](#%EF%B8%8F-variables-de-entorno)
- [⚡ Optimizaciones Incluidas](#-optimizaciones-incluidas)
- [🔧 Mantenimiento](#-mantenimiento)
- [🛡️ Seguridad](#%EF%B8%8F-seguridad)
- [📚 Tecnologías](#-tecnologías)

---

## 🚀 Despliegue en Railway

### Paso 1: Fork y Conectar

1. Haz **Fork** de este repositorio
2. En [Railway](https://railway.app), crea un nuevo proyecto
3. Selecciona **"Deploy from GitHub repo"**
4. Conecta tu fork y selecciona la rama `deploy`

### Paso 2: Agregar Base de Datos

1. En Railway, haz clic en **"+ New"** → **"Database"** → **"MySQL"**
2. Railway proporcionará automáticamente la variable `DATABASE_URL`

### Paso 3: Configurar Variables de Entorno

En la pestaña **Variables** de tu servicio, agrega:

```env
APP_NAME=TecnoGest
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-app.up.railway.app
APP_KEY=   # Se genera automáticamente o usa: php artisan key:generate --show

DB_CONNECTION=mysql
MYSQL_URL=${DATABASE_URL}   # Railway lo provee automáticamente

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=warning
```

### Paso 4: Desplegar

Railway detectará automáticamente el `railway.json` y:

1. **Build:** Instala dependencias, compila assets, cachea configuraciones
2. **Start:** Ejecuta migraciones y levanta el servidor

### 🎉 ¡Listo

Accede en la URL que Railway te asigne.

**Credenciales por defecto:**

```
Email:    admin@tecnogest.com
Password: password
```

> ⚠️ **Cambia la contraseña del admin inmediatamente en producción.**

---

## ⚙️ Variables de Entorno

| Variable | Descripción | Valor por Defecto |
|----------|-------------|-------------------|
| `APP_NAME` | Nombre de la aplicación | `TecnoGest` |
| `APP_ENV` | Entorno de ejecución | `production` |
| `APP_DEBUG` | Modo debug | `false` |
| `APP_URL` | URL pública de la app | - |
| `APP_KEY` | Clave de encriptación | Auto-generada |
| `DB_CONNECTION` | Driver de BD | `mysql` |
| `MYSQL_URL` | URL de conexión MySQL | Provista por Railway |
| `CACHE_STORE` | Driver de caché | `database` |
| `SESSION_DRIVER` | Driver de sesiones | `database` |
| `LOG_LEVEL` | Nivel de logging | `warning` |

---

## ⚡ Optimizaciones Incluidas

Esta rama incluye las siguientes optimizaciones respecto a `main`:

### Rendimiento

- **OPcache** configurado para producción (sin validación de timestamps)
- **Config/Route/View cache** generados en build time
- **Filament optimize** y **icons cache** pre-compilados
- **Composer autoloader** optimizado con classmap authoritative
- **Eloquent strict mode** activado en producción (previene N+1)
- **MorphMap** configurado para reducir tamaño de datos polimórficos

### Build (railway.json)

```json
{
    "build": {
        "buildCommand": "composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan filament:optimize && php artisan icons:cache && php artisan config:cache && php artisan route:cache && php artisan view:cache"
    },
    "deploy": {
        "startCommand": "php artisan migrate --force && php artisan db:seed --class=DemoSeeder --force 2>/dev/null; php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
    }
}
```

### Seguridad

- `APP_DEBUG=false` enforced
- Headers de seguridad (X-Frame-Options, X-Content-Type-Options, etc.)
- Trust proxies configurado para Railway (`*`)
- CSRF protection activa
- Lazy loading prevention en producción

---

## 🔧 Mantenimiento

### Ver Logs

En el dashboard de Railway → tu servicio → pestaña **Logs**

### Ejecutar Comandos Artisan

```bash
# En Railway CLI
railway run php artisan migrate:status
railway run php artisan tinker
railway run php artisan cache:clear
```

### Resetear Base de Datos (Demo)

```bash
railway run php artisan migrate:fresh --seed --seeder=DemoSeeder --force
```

### Actualizar

1. Haz push a la rama `deploy` de tu fork
2. Railway desplegará automáticamente

---

## 🛡️ Seguridad

### En Producción

1. **Cambia la contraseña del admin:**

   ```bash
   railway run php artisan tinker
   >>> User::where('email', 'admin@tecnogest.com')->first()->update(['password' => Hash::make('TuPasswordSegura')]);
   ```

2. **Verifica las variables:**
   - `APP_DEBUG=false`
   - `APP_ENV=production`
   - `APP_KEY` generada y única

3. **Railway provee HTTPS automáticamente** — no requiere configuración adicional.

---

## 📚 Tecnologías

| Categoría | Tecnología | Versión |
|-----------|-----------|---------|
| **Backend** | Laravel | 12.x |
| **Admin Panel** | Filament PHP | 4.x |
| **Lenguaje** | PHP | 8.4 |
| **Base de Datos** | MySQL | 8.0+ |
| **Frontend** | Livewire + TailwindCSS | 3.x / 4.x |
| **Build Tool** | Vite | 7.x |
| **Deploy** | Railway (Nixpacks) | - |

---

## 🌿 Otras Ramas

| Rama | Propósito |
|------|-----------|
| [`main`](https://github.com/Gzus-cmd/TecnoGest/tree/main) | Desarrollo local (sin optimizaciones de producción) |
| [`docker`](https://github.com/Gzus-cmd/TecnoGest/tree/docker) | Despliegue con Docker multi-variante (MySQL, PostgreSQL, SQLite) |
| `deploy` (esta) | Despliegue optimizado para Railway |

---

<div align="center">

**TecnoGest** © 2025 - Sistema de Gestión de Inventario Tecnológico

Desarrollado por [Gzus-cmd](https://github.com/Gzus-cmd)

⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub

