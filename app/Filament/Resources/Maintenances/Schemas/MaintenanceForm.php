<?php

namespace App\Filament\Resources\Maintenances\Schemas;

use App\Models\Computer;
use App\Models\Location;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\Peripheral;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;


class MaintenanceForm
{
    /**
     * Crea un tipo de MorphToSelect con búsqueda personalizada por serial y ubicación
     */
    private static function makeSearchableDeviceType(
        string $modelClass,
        string $label
    ): MorphToSelect\Type {
        return MorphToSelect\Type::make($modelClass)
            ->label($label)
            ->getSearchResultsUsing(function (string $search) use ($modelClass): array {
                return $modelClass::query()
                    ->whereIn('status', ['Activo', 'Inactivo'])
                    ->where(function ($query) use ($search) {
                        $query->where('serial', 'like', "%{$search}%")
                            ->orWhereHas('location', function ($q) use ($search) {
                                $q->where('pavilion', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%");
                            });
                    })
                    ->with('location')
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn ($record) => [
                        $record->getKey() => "{$record->serial} [{$record->status}] | {$record->location->pavilion} - {$record->location->name}"
                    ])
                    ->toArray();
            })
            ->getOptionLabelUsing(function ($value) use ($modelClass): ?string {
                $record = $modelClass::with('location')->find($value);
                return $record ? "{$record->serial} [{$record->status}] | {$record->location->pavilion} - {$record->location->name}" : null;
            });
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Mantenimiento')
                    ->description('Datos básicos del mantenimiento')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Tipo')
                                    ->options([
                                        'Preventivo' => 'Preventivo',
                                        'Correctivo' => 'Correctivo',
                                    ])
                                    ->required(),
                                Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'Pendiente' => 'Pendiente',
                                        'En Proceso' => 'En proceso',
                                        'Finalizado' => 'Finalizado',
                                    ])
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Checkbox::make('requires_workshop')
                                    ->label('Requiere Traslado a Taller de Informática')
                                    ->helperText('Si se marca, el dispositivo será trasladado automáticamente al taller seleccionado')
                                    ->reactive()
                                    ->default(false),
                                
                                Select::make('workshop_location_id')
                                    ->label('Taller de Destino')
                                    ->options(function () {
                                        return Location::where('is_workshop', true)
                                            ->get()
                                            ->mapWithKeys(fn ($loc) => [$loc->id => "{$loc->pavilion} - {$loc->name}"]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(fn ($get) => $get('requires_workshop'))
                                    ->visible(fn ($get) => $get('requires_workshop'))
                                    ->helperText('Seleccione el taller al que se trasladará el dispositivo'),
                            ]),
                    ]),

                Section::make('Dispositivo y Técnico')
                    ->description('Información del dispositivo a mantener')
                    ->schema([
                        MorphToSelect::make('deviceable')
                            ->types([
                                self::makeSearchableDeviceType(Computer::class, 'Computadora'),
                                self::makeSearchableDeviceType(Printer::class, 'Impresora'),
                                self::makeSearchableDeviceType(Projector::class, 'Proyector'),
                                self::makeSearchableDeviceType(Peripheral::class, 'Periférico'),
                            ])
                            ->label('Seleccionar Dispositivo')
                            ->searchable()
                            ->required()
                    ]),

                Section::make('Descripción')
                    ->description('Detalles del trabajo realizado')
                    ->schema([
                        Textarea::make('description')
                            ->label('Descripción del Mantenimiento')
                            ->placeholder('Describa el trabajo realizado...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
