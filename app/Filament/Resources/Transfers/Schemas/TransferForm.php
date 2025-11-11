<?php

namespace App\Filament\Resources\Transfers\Schemas;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
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
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Dispositivo')
                    ->description('Datos del dispositivo a trasladar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                MorphToSelect::make('deviceable')
                            ->types([
                                MorphToSelect\Type::make(Computer::class)
                                    ->label('Computadora')
                                    ->modifyOptionsQueryUsing(fn ($query) => $query
                                        ->whereNotIn('status', ['Desmantelado', 'En Mantenimiento'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Computer $record): string => "{$record->location->pavilion} - {$record->location->name} | {$record->serial}"),
                                MorphToSelect\Type::make(Printer::class)
                                    ->label('Impresora')
                                    ->modifyOptionsQueryUsing(fn ($query) => $query
                                        ->whereNotIn('status', ['Desmantelado', 'En Mantenimiento'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Printer $record): string => "{$record->location->pavilion} - {$record->location->name} | {$record->serial}"),
                                MorphToSelect\Type::make(Projector::class)
                                    ->label('Proyector')
                                    ->modifyOptionsQueryUsing(fn ($query) => $query
                                        ->whereNotIn('status', ['Desmantelado', 'En Mantenimiento'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Projector $record): string => "{$record->location->pavilion} - {$record->location->name} | {$record->serial}"),
                            ])
                            ->label('Seleccionar Dispositivo')
                            ->searchable()
                            ->required()
                            ]),
                    ]),

                Section::make('Ubicaciones y Responsable')
                    ->description('Origen, destino y responsable del traslado')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                
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
                            ->required()
                            ->placeholder('Describa el motivo del traslado...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
