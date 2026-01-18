<?php

namespace Database\Seeders;

use App\Models\Printer;
use App\Models\Projector;
use App\Models\Component;
use App\Models\SparePart;
use App\Models\Keyboard;
use App\Models\Mouse;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class PrinterProjectorComponentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Creando componentes para impresoras y proyectores...');

        $printers = Printer::with('modelo')->get();
        $projectors = Projector::with('modelo')->get();
        $provider = Provider::first() ?? Provider::factory()->create();

        if ($printers->isEmpty()) {
            $this->command->warn('⚠️ No hay impresoras. Ejecuta PrinterSeeder primero.');
        } else {
            $this->createPrinterComponents($printers, $provider);
        }

        if ($projectors->isEmpty()) {
            $this->command->warn('⚠️ No hay proyectores. Ejecuta ProjectorSeeder primero.');
        } else {
            $this->createProjectorComponents($projectors, $provider);
        }
    }

    private function createPrinterComponents($printers, $provider): void
    {
        $componentsCreated = 0;

        foreach ($printers as $printer) {
            // 70% de probabilidad de tener componentes
            if (rand(1, 100) <= 70) {
                // Crear teclado para la impresora (para configuración)
                $keyboard = Keyboard::create([
                    'brand' => collect(['Logitech', 'HP', 'Dell'])->random(),
                    'model' => 'K' . rand(100, 900),
                    'connection' => collect(['USB', 'Wireless'])->random(),
                    'language' => 'Español',
                ]);

                $component = Component::create([
                    'serial' => 'KB-PRINT-' . str_pad($printer->id, 5, '0', STR_PAD_LEFT),
                    'componentable_type' => 'Keyboard',
                    'componentable_id' => $keyboard->id,
                    'status' => 'Operativo',
                    'provider_id' => $provider->id,
                    'warranty_months' => 12,
                    'input_date' => $printer->input_date,
                ]);

                $component->printers()->attach($printer->id, [
                    'assigned_at' => $printer->input_date,
                    'status' => 'Vigente',
                    'assigned_by' => 1,
                ]);

                $componentsCreated++;

                // 50% de probabilidad de tener mouse también
                if (rand(1, 100) <= 50) {
                    $mouse = Mouse::create([
                        'brand' => collect(['Logitech', 'HP', 'Microsoft'])->random(),
                        'model' => 'M' . rand(100, 900),
                        'connection' => collect(['USB', 'Wireless'])->random(),
                    ]);

                    $component = Component::create([
                        'serial' => 'MS-PRINT-' . str_pad($printer->id, 5, '0', STR_PAD_LEFT),
                        'componentable_type' => 'Mouse',
                        'componentable_id' => $mouse->id,
                        'status' => 'Operativo',
                        'provider_id' => $provider->id,
                        'warranty_months' => 12,
                        'input_date' => $printer->input_date,
                    ]);

                    $component->printers()->attach($printer->id, [
                        'assigned_at' => $printer->input_date,
                        'status' => 'Vigente',
                        'assigned_by' => 1,
                    ]);

                    $componentsCreated++;
                }
            }

            // Crear algunos repuestos para la impresora (usar repuestos existentes)
            if (rand(1, 100) <= 40) {
                // Buscar un repuesto compatible aleatorio
                $sparePart = SparePart::where('type', 'Impresora')->inRandomOrder()->first();

                if ($sparePart) {
                    $component = Component::create([
                        'serial' => 'SP-PRINT-' . str_pad($printer->id, 5, '0', STR_PAD_LEFT) . '-' . rand(1, 99),
                        'componentable_type' => 'SparePart',
                        'componentable_id' => $sparePart->id,
                        'status' => 'Operativo',
                        'provider_id' => $provider->id,
                        'warranty_months' => 6,
                        'input_date' => $printer->input_date,
                    ]);

                    $component->printers()->attach($printer->id, [
                        'assigned_at' => $printer->input_date,
                        'status' => 'Vigente',
                        'assigned_by' => 1,
                    ]);

                    $componentsCreated++;
                }
            }
        }

        $this->command->info("✅ Componentes de impresoras creados: {$componentsCreated}");
    }

    private function createProjectorComponents($projectors, $provider): void
    {
        $componentsCreated = 0;

        foreach ($projectors as $projector) {
            // 60% de probabilidad de tener componentes
            if (rand(1, 100) <= 60) {
                // Crear mouse para control del proyector
                $mouse = Mouse::create([
                    'brand' => collect(['Logitech', 'Microsoft', 'Trust'])->random(),
                    'model' => 'M' . rand(100, 900),
                    'connection' => 'Wireless',
                ]);

                $component = Component::create([
                    'serial' => 'MS-PROJ-' . str_pad($projector->id, 5, '0', STR_PAD_LEFT),
                    'componentable_type' => 'Mouse',
                    'componentable_id' => $mouse->id,
                    'status' => 'Operativo',
                    'provider_id' => $provider->id,
                    'warranty_months' => 12,
                    'input_date' => $projector->input_date,
                ]);

                $component->projectors()->attach($projector->id, [
                    'assigned_at' => $projector->input_date,
                    'status' => 'Vigente',
                    'assigned_by' => 1,
                ]);

                $componentsCreated++;
            }

            // Crear repuestos para el proyector (usar repuestos existentes)
            if (rand(1, 100) <= 50) {
                // Buscar un repuesto compatible aleatorio
                $sparePart = SparePart::where('type', 'Proyector')->inRandomOrder()->first();

                if ($sparePart) {
                    $component = Component::create([
                        'serial' => 'SP-PROJ-' . str_pad($projector->id, 5, '0', STR_PAD_LEFT) . '-' . rand(1, 99),
                        'componentable_type' => 'SparePart',
                        'componentable_id' => $sparePart->id,
                        'status' => 'Operativo',
                        'provider_id' => $provider->id,
                        'warranty_months' => 6,
                        'input_date' => $projector->input_date,
                    ]);

                    $component->projectors()->attach($projector->id, [
                        'assigned_at' => $projector->input_date,
                        'status' => 'Vigente',
                        'assigned_by' => 1,
                    ]);

                    $componentsCreated++;
                }
            }
        }

        $this->command->info("✅ Componentes de proyectores creados: {$componentsCreated}");
    }
}
