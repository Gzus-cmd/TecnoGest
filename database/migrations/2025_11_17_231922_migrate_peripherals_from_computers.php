<?php

use App\Models\Computer;
use App\Models\Peripheral;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener todas las computadoras con sus componentes
        $computers = Computer::with('allComponents')->get();
        
        $peripheralCounter = 1;
        
        foreach ($computers as $computer) {
            // Filtrar componentes periféricos
            $peripheralComponents = $computer->allComponents->filter(function($component) {
                return in_array($component->componentable_type, [
                    'App\Models\Monitor',
                    'App\Models\Keyboard',
                    'App\Models\Mouse',
                    'App\Models\AudioDevice',
                    'App\Models\Stabilizer',
                    'App\Models\Splitter',
                ]);
            });
            
            // Si tiene componentes periféricos, crear Peripheral
            if ($peripheralComponents->isNotEmpty()) {
                $peripheral = Peripheral::create([
                    'code' => 'PER-' . str_pad($peripheralCounter, 3, '0', STR_PAD_LEFT),
                    'location_id' => $computer->location_id,
                    'computer_id' => $computer->id,
                    'status' => $computer->status === 'En Mantenimiento' ? 'Activo' : $computer->status,
                    'notes' => 'Migrado automáticamente desde Computer #' . $computer->id,
                ]);
                
                // Reasignar componentes periféricos a Peripheral
                foreach ($peripheralComponents as $component) {
                    DB::table('componentables')
                        ->where('component_id', $component->id)
                        ->where('componentable_type', 'App\Models\Computer')
                        ->where('componentable_id', $computer->id)
                        ->update([
                            'componentable_type' => 'App\Models\Peripheral',
                            'componentable_id' => $peripheral->id,
                        ]);
                }
                
                // Asignar peripheral_id al Computer
                $computer->update(['peripheral_id' => $peripheral->id]);
                
                $peripheralCounter++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Obtener todos los peripherals
        $peripherals = Peripheral::with('allComponents')->get();
        
        foreach ($peripherals as $peripheral) {
            if ($peripheral->computer_id) {
                $computer = $peripheral->computer;
                
                // Regresar componentes al Computer
                foreach ($peripheral->allComponents as $component) {
                    DB::table('componentables')
                        ->where('component_id', $component->id)
                        ->where('componentable_type', 'App\Models\Peripheral')
                        ->where('componentable_id', $peripheral->id)
                        ->update([
                            'componentable_type' => 'App\Models\Computer',
                            'componentable_id' => $computer->id,
                        ]);
                }
                
                // Limpiar peripheral_id del Computer
                $computer->update(['peripheral_id' => null]);
            }
            
            // Eliminar peripheral
            $peripheral->delete();
        }
    }
};
