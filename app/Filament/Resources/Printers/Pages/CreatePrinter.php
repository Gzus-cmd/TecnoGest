<?php

namespace App\Filament\Resources\Printers\Pages;

use App\Filament\Resources\Printers\PrinterResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatePrinter extends CreateRecord
{
    protected static ?string $title = 'Registrar Impresora';

    protected static string $resource = PrinterResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extraer el estabilizador antes de crear el registro
        $stabilizerComponentId = $data['stabilizer_component_id'] ?? null;

        // Eliminar del array de datos principales
        unset($data['stabilizer_component_id']);

        // Crear la impresora
        $printer = static::getModel()::create($data);

        // Asignar estabilizador si existe
        if ($stabilizerComponentId) {
            $printer->components()->attach($stabilizerComponentId, [
                'assigned_at' => now(),
                'status' => 'Vigente',
                'assigned_by' => Auth::id(),
            ]);
        }

        return $printer;
    }
}
