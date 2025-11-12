<?php

namespace App\Filament\Resources\Projectors\Pages;

use App\Filament\Resources\Projectors\ProjectorResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateProjector extends CreateRecord
{

    protected static ?string $title = 'Registrar Proyector';

    protected static string $resource = ProjectorResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extraer el estabilizador antes de crear el registro
        $stabilizerComponentId = $data['stabilizer_component_id'] ?? null;

        // Eliminar del array de datos principales
        unset($data['stabilizer_component_id']);

        // Crear el proyector
        $projector = static::getModel()::create($data);

        // Asignar estabilizador si existe
        if ($stabilizerComponentId) {
            $projector->components()->attach($stabilizerComponentId, [
                'assigned_at' => now(),
                'status' => 'Vigente',
                'assigned_by' => Auth::id(),
            ]);
        }

        return $projector;
    }

}
