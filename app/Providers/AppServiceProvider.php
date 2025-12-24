<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // OPTIMIZACIONES DE ELOQUENT
        // ═══════════════════════════════════════════════════════════════
        
        // En producción: prevenir lazy loading (detectar N+1)
        // En desarrollo: solo registrar warnings
        if ($this->app->environment('production')) {
            Model::preventLazyLoading(true);
        } else {
            // En desarrollo, registrar lazy loading pero no lanzar excepción
            Model::preventLazyLoading(false);
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                logger()->warning("Lazy loading detectado: {$model}::{$relation}");
            });
        }
        
        // Prevenir mass assignment silencioso (seguridad)
        Model::preventSilentlyDiscardingAttributes(true);
        
        // Prevenir acceso a atributos inexistentes
        Model::preventAccessingMissingAttributes(true);
    }
}
