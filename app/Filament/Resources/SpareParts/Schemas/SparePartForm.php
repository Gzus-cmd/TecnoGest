<?php

namespace App\Filament\Resources\SpareParts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class SparePartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Repuesto')
                    ->description('Modelo/tipo de repuesto para catálogo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Tipo de Repuesto')
                                    ->options([
                                        'Placa Base' => 'Placa Base',
                                        'Procesador' => 'Procesador',
                                        'Tarjeta Gráfica' => 'Tarjeta Gráfica',
                                        'Memoria RAM' => 'Memoria RAM',
                                        'Almacenamiento' => 'Almacenamiento',
                                        'Monitor' => 'Monitor',
                                        'Teclado' => 'Teclado',
                                        'Mouse' => 'Mouse',
                                        'Adaptador de Red' => 'Adaptador de Red',
                                        'Fuente de Poder' => 'Fuente de Poder',
                                        'Gabinete' => 'Gabinete',
                                        'Dispositivo de Audio' => 'Dispositivo de Audio',
                                        'Estabilizador' => 'Estabilizador',
                                        'Splitter' => 'Splitter',
                                        'Cabezal de Impresión' => 'Cabezal de Impresión',
                                        'Rodillo' => 'Rodillo',
                                        'Fusor' => 'Fusor',
                                        'Lámpara de Proyector' => 'Lámpara de Proyector',
                                        'Lente' => 'Lente',
                                        'Ventilador' => 'Ventilador',
                                        'Otro' => 'Otro',
                                    ])
                                    ->searchable()
                                    ->required(),

                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('part_number')
                                    ->label('Número de Parte')
                                    ->maxLength(255),
                            ]),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        KeyValue::make('specifications')
                            ->label('Especificaciones Técnicas')
                            ->keyLabel('Característica')
                            ->valueLabel('Valor')
                            ->addButtonLabel('Agregar especificación')
                            ->columnSpanFull()
                            ->helperText('Ejemplo: Capacidad → 8GB, Velocidad → 3200MHz, etc.'),
                    ]),
            ]);
    }
}
