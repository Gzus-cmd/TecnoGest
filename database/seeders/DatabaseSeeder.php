<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder Principal - Por defecto ejecuta DemoSeeder
 * 
 * MODOS DE USO:
 * 
 * 1. DESARROLLO/DEMO (datos completos de prueba):
 *    php artisan db:seed
 *    php artisan db:seed --class=DemoSeeder
 *    php artisan migrate:fresh --seed
 * 
 * 2. PRODUCCIÓN (solo configuración inicial):
 *    php artisan db:seed --class=ProductionSeeder
 *    php artisan migrate:fresh --seed --seeder=ProductionSeeder
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Por defecto usa DemoSeeder (desarrollo/pruebas)
        $this->call(DemoSeeder::class);
    }
}
