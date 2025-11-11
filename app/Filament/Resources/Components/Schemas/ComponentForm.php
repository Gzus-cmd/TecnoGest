<?php

namespace App\Filament\Resources\Components\Schemas;

use App\Models\CPU;
use App\Models\GPU;
use App\Models\RAM;
use App\Models\ROM;
use App\Models\Motherboard;
use App\Models\PowerSupply;
use App\Models\NetworkAdapter;
use App\Models\TowerCase;
use App\Models\Monitor;
use App\Models\Keyboard;
use App\Models\Mouse;
use App\Models\AudioDevice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\MorphToSelect;


class ComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tipo de Componente')
                    ->description('Selecciona el tipo de hardware que deseas registrar')
                    ->schema([
                        MorphToSelect::make('componentable')
                            ->types([
                                MorphToSelect\Type::make(CPU::class)
                                    ->label('Procesador (CPU)')
                                    ->getOptionLabelFromRecordUsing(fn (CPU $record): string => "{$record->brand} - {$record->model} ({$record->cores} núcleos)"),
                                MorphToSelect\Type::make(GPU::class)
                                    ->label('Tarjeta Gráfica (GPU)')
                                    ->getOptionLabelFromRecordUsing(fn (GPU $record): string => "{$record->brand} - {$record->model} ({$record->memory}GB)"),
                                MorphToSelect\Type::make(RAM::class)
                                    ->label('Memoria RAM')
                                    ->getOptionLabelFromRecordUsing(fn (RAM $record): string => "{$record->brand} - {$record->capacity}GB {$record->type}"),
                                MorphToSelect\Type::make(Motherboard::class)
                                    ->label('Placa Base')
                                    ->getOptionLabelFromRecordUsing(fn (Motherboard $record): string => "{$record->brand} - {$record->model}"),
                                MorphToSelect\Type::make(ROM::class)
                                    ->label('Almacenamiento')
                                    ->getOptionLabelFromRecordUsing(fn (ROM $record): string => "{$record->brand} - {$record->capacity}GB {$record->type}"),
                                MorphToSelect\Type::make(PowerSupply::class)
                                    ->label('Fuente de Poder')
                                    ->getOptionLabelFromRecordUsing(fn (PowerSupply $record): string => "{$record->brand} - {$record->watts}W"),
                                MorphToSelect\Type::make(NetworkAdapter::class)
                                    ->label('Adaptador de Red')
                                    ->getOptionLabelFromRecordUsing(fn (NetworkAdapter $record): string => "{$record->brand} - {$record->model}"),
                                MorphToSelect\Type::make(TowerCase::class)
                                    ->label('Gabinete')
                                    ->getOptionLabelFromRecordUsing(fn (TowerCase $record): string => "{$record->brand} - {$record->model}"),
                                MorphToSelect\Type::make(Monitor::class)
                                    ->label('Monitor')
                                    ->getOptionLabelFromRecordUsing(fn (Monitor $record): string => "{$record->brand} - {$record->size}\""),
                                MorphToSelect\Type::make(Keyboard::class)
                                    ->label('Teclado')
                                    ->getOptionLabelFromRecordUsing(fn (Keyboard $record): string => "{$record->brand} - {$record->model}"),
                                MorphToSelect\Type::make(Mouse::class)
                                    ->label('Ratón')
                                    ->getOptionLabelFromRecordUsing(fn (Mouse $record): string => "{$record->brand} - {$record->model}"),
                                MorphToSelect\Type::make(AudioDevice::class)
                                    ->label('Dispositivo de Audio')
                                    ->getOptionLabelFromRecordUsing(fn (AudioDevice $record): string => "{$record->brand} - {$record->model}"),
                            ])
                            ->label('Seleccionar Hardware')
                            ->required()
                    ])
                    ->columns(1),

                Section::make('Información Básica del Componente')
                    ->description('Datos de identificación y garantía')
                    ->schema([
                        TextInput::make('serial')
                            ->label('Número de Serie')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->placeholder('Ej: ABC123456789'),
                        Grid::make(2)
                            ->schema([
                                Select::make('provider_id')
                                    ->relationship('provider', 'name')
                                    ->label('Proveedor')
                                    ->searchable()
                                    ->required(),
                                TextInput::make('warranty_months')
                                    ->label('Meses de Garantía')
                                    ->numeric()
                                    ->placeholder('Ej: 24'),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Fechas Importantes')
                    ->description('Registro de entrada y salida del inventario')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('input_date')
                                    ->label('Fecha de Ingreso')
                                    ->required(),
                                DatePicker::make('output_date')
                                    ->label('Fecha de Retiro'),
                            ]),
                    ]),

                Section::make('Estado del Componente')
                    ->description('Condición actual del hardware')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'Operativo' => '✓ Operativo',
                                'Deficiente' => '⚠ Deficiente',
                                'Retirado' => '✗ Retirado'
                            ])
                            ->required(),
                    ]),
            ]);
    }
}
