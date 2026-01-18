<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePeripheralTypes extends Command
{
    protected $signature = 'db:update-peripheral-types';
    protected $description = 'Actualiza los tipos de periféricos eliminando el prefijo App\\Models\\';

    public function handle()
    {
        $this->info('🔄 Actualizando tipos de periféricos...');

        $tables = ['printers', 'projectors'];
        $totalUpdated = 0;

        foreach ($tables as $table) {
            $this->info("Procesando tabla: {$table}");

            $updated = DB::table($table)
                ->where('deviceable_type', 'like', 'App\\Models\\%')
                ->update([
                    'deviceable_type' => DB::raw("REPLACE(deviceable_type, 'App\\\\Models\\\\', '')")
                ]);

            $this->line("  - Registros actualizados: {$updated}");
            $totalUpdated += $updated;
        }

        if ($totalUpdated > 0) {
            $this->info("✅ Total de registros actualizados: {$totalUpdated}");
        } else {
            $this->comment('ℹ️  No se encontraron registros para actualizar.');
        }

        return 0;
    }
}
