<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        // MORPH MAP - Reducir prefijos App\Models\ en DB
        // ═══════════════════════════════════════════════════════════════
        Relation::morphMap([
            // Dispositivos
            'Computer' => \App\Models\Computer::class,
            'Printer' => \App\Models\Printer::class,
            'Projector' => \App\Models\Projector::class,
            'Peripheral' => \App\Models\Peripheral::class,

            // Componentes de Hardware
            'Motherboard' => \App\Models\Motherboard::class,
            'CPU' => \App\Models\CPU::class,
            'GPU' => \App\Models\GPU::class,
            'RAM' => \App\Models\RAM::class,
            'ROM' => \App\Models\ROM::class,
            'PowerSupply' => \App\Models\PowerSupply::class,
            'NetworkAdapter' => \App\Models\NetworkAdapter::class,
            'TowerCase' => \App\Models\TowerCase::class,

            // Periféricos
            'Monitor' => \App\Models\Monitor::class,
            'Keyboard' => \App\Models\Keyboard::class,
            'Mouse' => \App\Models\Mouse::class,
            'AudioDevice' => \App\Models\AudioDevice::class,
            'Stabilizer' => \App\Models\Stabilizer::class,
            'Splitter' => \App\Models\Splitter::class,

            // Otros
            'SparePart' => \App\Models\SparePart::class,
        ]);

        // ═══════════════════════════════════════════════════════════════
        // GATE PARA SUPER ADMIN (Filament Shield)
        // ═══════════════════════════════════════════════════════════════
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

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
