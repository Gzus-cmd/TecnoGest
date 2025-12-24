<?php

namespace App\Filament\Resources\Projectors\Pages;

use App\Filament\Resources\Projectors\ProjectorResource;
use App\Models\Projector;
use App\Models\Maintenance;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListProjectors extends ListRecords
{
    protected static string $resource = ProjectorResource::class;

    protected static ?string $title = 'Lista de Proyectores';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Proyector'),
            
            Action::make('preventive_maintenance')
                ->visible(fn () => auth()->user()?->can('ProjectorPreventiveMaintenance'))
                ->label('Preventivo')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Registrar Mantenimiento Preventivo')
                ->modalDescription('Se creará un registro de mantenimiento preventivo pendiente para todos los proyectores activos.')
                ->modalIcon('heroicon-o-wrench-screwdriver')
                ->form([
                    Textarea::make('description')
                        ->label('Descripción del Mantenimiento')
                        ->placeholder('Ejemplo: Limpieza de lente, revisión de lámpara, limpieza de filtros...')
                        ->required()
                        ->rows(3)
                        ->helperText('Esta descripción se aplicará a todos los mantenimientos creados.'),
                ])
                ->action(function (array $data): void {
                    $activeProjectors = Projector::where('status', 'Activo')->get();
                    
                    if ($activeProjectors->isEmpty()) {
                        Notification::make()
                            ->warning()
                            ->title('Sin Proyectores Activos')
                            ->body('No hay proyectores activos para registrar mantenimiento.')
                            ->send();
                        return;
                    }
                    
                    $count = 0;
                    foreach ($activeProjectors as $projector) {
                        Maintenance::create([
                            'type' => 'Preventivo',
                            'deviceable_type' => Projector::class,
                            'deviceable_id' => $projector->id,
                            'registered_by' => Auth::id(),
                            'status' => 'Pendiente',
                            'description' => $data['description'],
                            'requires_workshop' => false,
                        ]);
                        $count++;
                    }
                    
                    Notification::make()
                        ->success()
                        ->title('Mantenimientos Registrados')
                        ->body("Se registraron {$count} mantenimientos preventivos pendientes.")
                        ->send();
                })
                ->modalSubmitActionLabel('Registrar Mantenimientos')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }
}
