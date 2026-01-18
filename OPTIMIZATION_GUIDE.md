# 🚀 Guía de Optimización de Rendimiento - TecnoGest

## Problema Detectado

- **Dashboard**: 15 segundos de carga
- **Páginas normales**: 3-4 segundos  
- **Historial de componentes**: 8 segundos
- **Uso de recursos**: 136MB RAM, 0.1 vCPU por usuario
- **Límite disponible**: 2 vCPU, 1GB RAM

## ✅ Optimizaciones Implementadas

### 1. Base de Datos (Prioridad Alta)

**Índices agregados:**

```bash
php artisan migrate
```

- ✅ Índices en `componentables` para queries del historial
- ✅ Índices en `components` para filtros frecuentes
- ✅ Índice en `serial` para búsquedas rápidas

**Impacto esperado:** Reducción del 60-80% en tiempo de consultas pesadas

### 2. ComponentHistory Resource (8s → <1s esperado)

**Optimizaciones aplicadas:**

- ✅ Eliminado eager loading innecesario en query principal
- ✅ Agregada paginación por defecto (25 registros)
- ✅ Defer loading activado (carga diferida)
- ✅ Caché estático para dispositivos durante misma request
- ✅ Optimizado getStateUsing para usar relaciones en lugar de queries

### 3. Caché de Assets Estáticos

**Middleware CacheAssets:**

- ✅ Assets versionados: cachear 1 año (immutable)
- ✅ Otros assets: cachear 1 semana
- ✅ HTML/JSON: no cachear

**Beneficio:** Reducir carga del servidor en ~70% tras primera visita

### 4. Dashboard Widgets  

**MonthlyActivity:**

- ✅ Caché de estadísticas por 5 minutos
- ✅ Query única con UNION para múltiples conteos

**CriticalComponents:**

- ✅ Paginación desactivada (solo 10 registros)
- ✅ Eager loading optimizado

**Impacto esperado:** Dashboard de 15s → 3-5s

### 5. Recursos de Dispositivos

**Eager Loading optimizado:**

- Computers: `with(['location', 'os', 'peripheral'])`
- Printers: `with(['location', 'modelo'])`
- Projectors: `with(['location', 'modelo'])`

## 📋 Pasos para Aplicar en Producción (Railway)

### Paso 1: Commit y Push

```bash
git add .
git commit -m "perf: optimización completa de rendimiento - índices, caché y queries"
git push origin deploy
```

### Paso 2: Ejecutar en Railway (después del deploy)

```bash
# Ejecutar migraciones con índices
php artisan migrate --force

# Optimizar Laravel para producción
./optimize-production.sh

# O manualmente:
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
php artisan icons:cache
```

### Paso 3: Variables de Entorno (Railway)

Asegúrate de tener configurado:

```env
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### Paso 4: Configuración NGINX (si aplica)

Si Railway usa nginx, agregar configuración de `nginx-optimization.conf`:

- Compresión gzip
- Headers de caché
- Desactivar logs para assets

## 📊 Mejoras Esperadas

| Página | Antes | Después (estimado) |
|--------|-------|-------------------|
| Dashboard | 15s | 3-5s (70% mejora) |
| Computadoras | 3-4s | 1-1.5s (60% mejora) |
| Componentes | 3-4s | 1-1.5s (60% mejora) |
| Historial | 8s | <1s (90% mejora) |

## 🔧 Optimizaciones Adicionales (Opcional)

### 1. Redis (Recomendado para >50 usuarios simultáneos)

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=tu-redis-host
```

### 2. CDN para Assets

Subir assets de `/build` a CDN (CloudFlare, AWS CloudFront)

### 3. Queue Workers

```bash
# En Railway, agregar worker process
php artisan queue:work --tries=3 --timeout=60
```

### 4. Lazy Loading en Tablas

Para tablas con >1000 registros, considerar:

- Infinite scroll en lugar de paginación
- Cargas por demanda (defer)

## 🐛 Debugging de Rendimiento

### Verificar queries lentas

```bash
php artisan debugbar:enable
# O en config/debugbar.php: 'enabled' => true
```

### Monitorear Railway

```bash
# Ver logs en tiempo real
railway logs

# Métricas de CPU/RAM
railway status
```

### Analizar queries específicas

```php
DB::enableQueryLog();
// ... tu código
dd(DB::getQueryLog());
```

## ✨ Consejos Finales

1. **Monitorear primer deploy**: Ver logs de Railway durante 10-15 minutos
2. **Cache warming**: Primera carga será lenta, luego rápida
3. **Invalidar caché si hay problemas**:

   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

4. **Verificar métricas**: Railway dashboard → Ver uso CPU/RAM antes/después

## 📞 Soporte

Si persisten problemas de rendimiento:

1. Revisar logs: `railway logs --follow`
2. Verificar índices: `SHOW INDEXES FROM componentables;`
3. Analizar queries: Activar debugbar temporalmente
4. Considerar upgrade de plan si >100 usuarios simultáneos

---

**Última actualización**: 2026-01-18  
**Versión optimizada**: 2.0
