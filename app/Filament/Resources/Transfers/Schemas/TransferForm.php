<?php

namespace App\Filament\Resources\Transfers\Schemas;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\Peripheral;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransferForm
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
                Section::make('Información del Dispositivo')
                    ->description('Datos del dispositivo a trasladar')
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

                Section::make('Ubicaciones y Estado')
                    ->description('Origen, destino y estado del traslado')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'Pendiente' => 'Pendiente',
                                        'En Proceso' => 'En Proceso',
                                        'Finalizado' => 'Finalizado',
                                    ])
                                    ->default('Pendiente')
                                    ->required(),
                                
                                Select::make('destiny_id')
                                    ->label('Ubicación Destino')
                                    ->relationship('destiny', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->pavilion} | {$record->name}")
                                    ->searchable()
                                    ->preload()
                                    ->options(function ($get) {
                                        // Obtener la ubicación actual del dispositivo seleccionado
                                        $deviceable = $get('deviceable');
                                        $deviceableType = $get('deviceable_type');
                                        
                                        if (!$deviceable || !$deviceableType) {
                                            return \App\Models\Location::all()->pluck('name', 'id');
                                        }
                                        
                                        // Obtener el modelo correcto basado en el tipo
                                        $model = match($deviceableType) {
                                            'App\\Models\\Computer' => Computer::find($deviceable),
                                            'App\\Models\\Printer' => Printer::find($deviceable),
                                            'App\\Models\\Projector' => Projector::find($deviceable),
                                            default => null
                                        };
                                        
                                        if (!$model) {
                                            return \App\Models\Location::all()->pluck('name', 'id');
                                        }
                                        
                                        // Excluir la ubicación actual del dispositivo
                                        return \App\Models\Location::where('id', '!=', $model->location_id)
                                            ->get()
                                            ->mapWithKeys(fn ($loc) => [$loc->id => "{$loc->pavilion} | {$loc->name}"]);
                                    })
                                    ->required(),
                            ]),
                                
                        DatePicker::make('date')
                            ->label('Fecha del Traslado')
                            ->required(),
                    ]),

                Section::make('Motivo del Traslado')
                    ->description('Razón del cambio de ubicación')
                    ->schema([
                        Textarea::make('reason')
                            ->label('Motivo')
                            ->placeholder('Describa el motivo del traslado...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
