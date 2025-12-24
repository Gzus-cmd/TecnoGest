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
use App\Models\SparePart;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\MorphToSelect;


class ComponentForm
{
    /**
     * Crea un tipo de MorphToSelect con búsqueda personalizada
     */
    private static function makeSearchableType(
        string $modelClass,
        string $label,
        array $searchFields,
        callable $labelFormatter
    ): MorphToSelect\Type {
        return MorphToSelect\Type::make($modelClass)
            ->label($label)
            ->getSearchResultsUsing(function (string $search) use ($modelClass, $searchFields, $labelFormatter): array {
                $query = $modelClass::query();
                
                foreach ($searchFields as $index => $field) {
                    if ($index === 0) {
                        $query->where($field, 'like', "%{$search}%");
                    } else {
                        $query->orWhere($field, 'like', "%{$search}%");
                    }
                }
                
                return $query->limit(50)
                    ->get()
                    ->mapWithKeys(fn ($record) => [
                        $record->getKey() => $labelFormatter($record)
                    ])
                    ->toArray();
            })
            ->getOptionLabelUsing(function ($value) use ($modelClass, $labelFormatter): ?string {
                $record = $modelClass::find($value);
                return $record ? $labelFormatter($record) : null;
            });
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tipo de Componente')
                    ->description('Selecciona el tipo de hardware que deseas registrar')
                    ->schema([
                        MorphToSelect::make('componentable')
                            ->types([
                                self::makeSearchableType(
                                    CPU::class,
                                    'Procesador (CPU)',
                                    ['brand', 'model', 'socket'],
                                    fn($r) => "{$r->brand} - {$r->model} ({$r->cores} núcleos)"
                                ),
                                self::makeSearchableType(
                                    GPU::class,
                                    'Tarjeta Gráfica (GPU)',
                                    ['brand', 'model', 'memory', 'interface'],
                                    fn($r) => "{$r->brand} - {$r->model} ({$r->interface} {$r->type} {$r->memory}GB)"
                                ),
                                self::makeSearchableType(
                                    RAM::class,
                                    'Memoria RAM',
                                    ['brand', 'model', 'type'],
                                    fn($r) => "{$r->brand} - {$r->model} ({$r->type} - {$r->capacity}GB)"
                                ),
                                self::makeSearchableType(
                                    Motherboard::class,
                                    'Placa Base',
                                    ['brand', 'model', 'socket'],
                                    fn($r) => "{$r->brand} - {$r->model}"
                                ),
                                self::makeSearchableType(
                                    ROM::class,
                                    'Almacenamiento',
                                    ['brand', 'type'],
                                    fn($r) => "{$r->brand} - {$r->capacity}GB {$r->type}"
                                ),
                                self::makeSearchableType(
                                    PowerSupply::class,
                                    'Fuente de Poder',
                                    ['brand'],
                                    fn($r) => "{$r->brand} - {$r->watts}W"
                                ),
                                self::makeSearchableType(
                                    NetworkAdapter::class,
                                    'Adaptador de Red',
                                    ['brand', 'model'],
                                    fn($r) => "{$r->brand} - {$r->model}"
                                ),
                                self::makeSearchableType(
                                    TowerCase::class,
                                    'Gabinete',
                                    ['brand', 'model'],
                                    fn($r) => "{$r->brand} - {$r->model}"
                                ),
                                self::makeSearchableType(
                                    Monitor::class,
                                    'Monitor',
                                    ['brand', 'model'],
                                    fn($r) => "{$r->brand} - {$r->size}"
                                ),
                                self::makeSearchableType(
                                    Keyboard::class,
                                    'Teclado',
                                    ['brand', 'model'],
                                    fn($r) => "{$r->brand} - {$r->model}"
                                ),
                                self::makeSearchableType(
                                    Mouse::class,
                                    'Ratón',
                                    ['brand', 'model'],
                                    fn($r) => "{$r->brand} - {$r->model}"
                                ),
                                self::makeSearchableType(
                                    AudioDevice::class,
                                    'Dispositivo de Audio',
                                    ['brand', 'model'],
                                    fn($r) => "{$r->brand} - {$r->model}"
                                ),
                                self::makeSearchableType(
                                    SparePart::class,
                                    'Repuesto (Impresora/Proyector)',
                                    ['brand', 'model', 'type'],
                                    fn($r) => "{$r->brand} - {$r->model} ({$r->type})"
                                ),
                            ])
                            ->label('Seleccionar Hardware')
                            ->searchable()
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
