<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMorphTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'morph:update-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los tipos polimórficos en la base de datos eliminando el prefijo App\\Models\\';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando actualización de tipos polimórficos...');

        $morphMap = [
            // Dispositivos
            'App\\Models\\Computer' => 'Computer',
            'App\\Models\\Printer' => 'Printer',
            'App\\Models\\Projector' => 'Projector',

            // Componentes de Hardware
            'App\\Models\\Motherboard' => 'Motherboard',
            'App\\Models\\CPU' => 'CPU',
            'App\\Models\\GPU' => 'GPU',
            'App\\Models\\RAM' => 'RAM',
            'App\\Models\\ROM' => 'ROM',
            'App\\Models\\PowerSupply' => 'PowerSupply',
            'App\\Models\\NetworkAdapter' => 'NetworkAdapter',
            'App\\Models\\TowerCase' => 'TowerCase',

            // Periféricos
            'App\\Models\\Monitor' => 'Monitor',
            'App\\Models\\Keyboard' => 'Keyboard',
            'App\\Models\\Mouse' => 'Mouse',
            'App\\Models\\AudioDevice' => 'AudioDevice',
            'App\\Models\\Stabilizer' => 'Stabilizer',
            'App\\Models\\Splitter' => 'Splitter',

            // Otros
            'App\\Models\\SparePart' => 'SparePart',
        ];

        $tables = [
            'components' => 'componentable_type',
            'componentables' => 'componentable_type',
            'maintenances' => 'deviceable_type',
            'transfers' => 'deviceable_type',
        ];

        $totalUpdates = 0;

        foreach ($tables as $table => $column) {
            $this->info("Actualizando tabla: {$table}");

            foreach ($morphMap as $oldType => $newType) {
                $count = DB::table($table)
                    ->where($column, $oldType)
                    ->update([$column => $newType]);

                if ($count > 0) {
                    $this->line("  ✓ {$oldType} → {$newType}: {$count} registros");
                    $totalUpdates += $count;
                }
            }
        }

        $this->newLine();
        $this->info("✅ Actualización completada!");
        $this->info("Total de registros actualizados: {$totalUpdates}");

        return Command::SUCCESS;
    }
}
