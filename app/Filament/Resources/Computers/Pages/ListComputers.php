<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use App\Models\Computer;
use App\Models\Maintenance;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListComputers extends ListRecords
{
    protected static string $resource = ComputerResource::class;

    protected static ?string $title = 'Lista de Computadoras';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Computadora'),
            
            Action::make('preventive_maintenance')
                ->visible(fn () => auth()->user()?->can('ComputerPreventiveMaintenance'))
                ->label('Preventivo')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Registrar Mantenimiento Preventivo')
                ->modalDescription('Se creará un registro de mantenimiento preventivo pendiente para todas las computadoras activas.')
                ->modalIcon('heroicon-o-wrench-screwdriver')
                ->form([
                    Textarea::make('description')
                        ->label('Descripción del Mantenimiento')
                        ->placeholder('Ejemplo: Limpieza general, actualización de software, revisión de hardware...')
                        ->required()
                        ->rows(3)
                        ->helperText('Esta descripción se aplicará a todos los mantenimientos creados.'),
                ])
                ->action(function (array $data): void {
                    $activeComputers = Computer::where('status', 'Activo')->get();
                    
                    if ($activeComputers->isEmpty()) {
                        Notification::make()
                            ->warning()
                            ->title('Sin Computadoras Activas')
                            ->body('No hay computadoras activas para registrar mantenimiento.')
                            ->send();
                        return;
                    }
                    
                    $count = 0;
                    foreach ($activeComputers as $computer) {
                        Maintenance::create([
                            'type' => 'Preventivo',
                            'deviceable_type' => Computer::class,
                            'deviceable_id' => $computer->id,
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
                        ->body("Se registraron {$count} mantenimientos de rutina pendientes.")
                        ->send();
                })
                ->modalSubmitActionLabel('Registrar Mantenimientos')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }
}
