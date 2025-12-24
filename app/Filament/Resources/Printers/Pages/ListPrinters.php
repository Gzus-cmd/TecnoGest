<?php

namespace App\Filament\Resources\Printers\Pages;

use App\Filament\Resources\Printers\PrinterResource;
use App\Models\Printer;
use App\Models\Maintenance;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPrinters extends ListRecords
{
    protected static string $resource = PrinterResource::class;

    protected static ?string $title = 'Lista de Impresoras';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Impresora'),
            
            Action::make('preventive_maintenance')
                ->visible(fn () => auth()->user()?->can('PrinterPreventiveMaintenance'))
                ->label('Preventivo')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Registrar Mantenimiento Preventivo')
                ->modalDescription('Se creará un registro de mantenimiento preventivo pendiente para todas las impresoras activas.')
                ->modalIcon('heroicon-o-wrench-screwdriver')
                ->form([
                    Textarea::make('description')
                        ->label('Descripción del Mantenimiento')
                        ->placeholder('Ejemplo: Limpieza de cabezales, cambio de tóner, revisión de rodillos...')
                        ->required()
                        ->rows(3)
                        ->helperText('Esta descripción se aplicará a todos los mantenimientos creados.'),
                ])
                ->action(function (array $data): void {
                    $activePrinters = Printer::where('status', 'Activo')->get();
                    
                    if ($activePrinters->isEmpty()) {
                        Notification::make()
                            ->warning()
                            ->title('Sin Impresoras Activas')
                            ->body('No hay impresoras activas para registrar mantenimiento.')
                            ->send();
                        return;
                    }
                    
                    $count = 0;
                    foreach ($activePrinters as $printer) {
                        Maintenance::create([
                            'type' => 'Preventivo',
                            'deviceable_type' => Printer::class,
                            'deviceable_id' => $printer->id,
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
