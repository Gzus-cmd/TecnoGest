<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CacheUserPermissions
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $cacheKey = "user_permissions_{$user->id}";
            
            // Cachear permisos por 5 minutos
            $permissions = Cache::remember($cacheKey, 300, function () use ($user) {
                return [
                    'all_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'is_super_admin' => $user->hasRole('super_admin'),
                ];
            });
            
            // Pre-cargar permisos en la sesión
            $user->cachedPermissions = $permissions['all_permissions'];
            $user->cachedRoles = $permissions['roles'];
            $user->isSuperAdmin = $permissions['is_super_admin'];
        }
        
        return $next($request);
    }
}
