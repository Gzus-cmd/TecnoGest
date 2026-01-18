<?php

namespace Database\Seeders;

use App\Models\Transfer;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Seeder;

class TransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $mainUser = $users->first();
        $locations = Location::where('is_workshop', false)->get();

        if (!$mainUser) {
            $this->command->warn('⚠️ No hay usuarios. Ejecuta DatabaseSeeder primero.');
            return;
        }

        if ($locations->count() < 2) {
            $this->command->warn('⚠️ Se necesitan al menos 2 ubicaciones. Ejecuta LocationSeeder primero.');
            return;
        }

        // Obtener más dispositivos activos (cargando relación location para evitar lazy loading)
        $computers = Computer::with('location')->whereIn('status', ['Activo', 'Inactivo'])->limit(10)->get();
        $printers = Printer::with('location')->whereIn('status', ['Activo', 'Inactivo'])->limit(6)->get();
        $projectors = Projector::with('location')->whereIn('status', ['Activo', 'Inactivo'])->limit(4)->get();

        $statuses = ['Pendiente', 'En Proceso', 'Finalizado'];
        $reasons = [
            'Reubicación por cambio de departamento',
            'Optimización de recursos',
            'Solicitud de usuario',
            'Redistribución de equipamiento',
            'Mantenimiento programado',
            'Reorganización de oficinas',
            'Actualización de infraestructura',
        ];

        $transfersCreated = 0;

        // Crear traslados para computadoras
        foreach ($computers as $computer) {
            $status = collect($statuses)->random();
            $origin = $computer->location;
            $destiny = $locations->where('id', '!=', $origin->id)->random();

            $createdAt = now()->subDays(rand(1, 90));
            $registeredBy = $users->random();

            Transfer::create([
                'deviceable_type' => Computer::class,
                'deviceable_id' => $computer->id,
                'registered_by' => $registeredBy->id,
                'origin_id' => $origin->id,
                'destiny_id' => $destiny->id,
                'date' => $createdAt->toDateString(),
                'reason' => collect($reasons)->random(),
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $status === 'Finalizado' ? $createdAt->addDays(rand(1, 3)) : $createdAt,
            ]);

            // Si el traslado está finalizado, actualizar la ubicación del dispositivo
            if ($status === 'Finalizado') {
                $computer->update(['location_id' => $destiny->id]);
            }

            $transfersCreated++;
        }

        // Crear traslados para impresoras
        foreach ($printers as $printer) {
            $status = collect($statuses)->random();
            $origin = $printer->location;
            $destiny = $locations->where('id', '!=', $origin->id)->random();

            $createdAt = now()->subDays(rand(1, 90));
            $registeredBy = $users->random();

            Transfer::create([
                'deviceable_type' => Printer::class,
                'deviceable_id' => $printer->id,
                'registered_by' => $registeredBy->id,
                'origin_id' => $origin->id,
                'destiny_id' => $destiny->id,
                'date' => $createdAt->toDateString(),
                'reason' => collect($reasons)->random(),
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $status === 'Finalizado' ? $createdAt->addDays(rand(1, 3)) : $createdAt,
            ]);

            if ($status === 'Finalizado') {
                $printer->update(['location_id' => $destiny->id]);
            }

            $transfersCreated++;
        }

        // Crear traslados para proyectores
        foreach ($projectors as $projector) {
            $status = collect($statuses)->random();
            $origin = $projector->location;
            $destiny = $locations->where('id', '!=', $origin->id)->random();

            $createdAt = now()->subDays(rand(1, 90));
            $registeredBy = $users->random();

            Transfer::create([
                'deviceable_type' => Projector::class,
                'deviceable_id' => $projector->id,
                'registered_by' => $registeredBy->id,
                'origin_id' => $origin->id,
                'destiny_id' => $destiny->id,
                'date' => $createdAt->toDateString(),
                'reason' => collect($reasons)->random(),
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $status === 'Finalizado' ? $createdAt->addDays(rand(1, 3)) : $createdAt,
            ]);

            if ($status === 'Finalizado') {
                $projector->update(['location_id' => $destiny->id]);
            }

            $transfersCreated++;
        }

        $this->command->info("✅ Traslados creados: {$transfersCreated}");
        $this->command->info("   📦 Estados: Pendiente, En Proceso, Finalizado");
        $this->command->info("   🚚 Entre " . $locations->count() . " ubicaciones diferentes");
        $this->command->info("   👥 Registrados por diferentes usuarios");
    }
}
