<?php

namespace App\Filament\Resources\Components\Schemas;

use App\Models\CPU;
use App\Models\GPU;
use Dom\Text;
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
                Section::make('Información de las Características')
                ->schema([
                

                    MorphToSelect::make('componentable')
                        ->types([
                            MorphToSelect\Type::make(CPU::class)
                                ->label('CPU')
                                ->getOptionLabelFromRecordUsing(fn (CPU $record): string => "{$record->brand} - {$record->model}"),
                            MorphToSelect\Type::make(GPU::class)
                                ->label('GPU')
                                ->getOptionLabelFromRecordUsing(fn (GPU $record): string => "{$record->brand} - {$record->model}"),
                        ])
                        ->label('Tipo de componente')
                        ->required()
                    ])

                    ,

                Section::make('Información Básica')
            ->schema([
                TextInput::make('serial')
                    ->required()
                    ->label('Número de Serie'),

                    Grid::make(2)
                ->schema([    
                        Select::make('provider_id')
                        ->relationship('provider', 'name')
                        ->required()
                        ->label('Proveedor')
                        ->searchable(),
                        TextInput::make('warranty_months')
                        ->numeric()
                        ->label('Meses de Garantía'),

                        ])
                        
                ])
                
                ,

                Section::make('Fechas Importantes')
            ->schema([
               
                Grid::make(2)
                ->schema([          DatePicker::make('input_date')
                                ->required()
                                ->label('Fecha de Ingreso'),
                            DatePicker::make('output_date')
                                ->label('Fecha de Retiro'),])
                ])

                ,

                Section::make('Estado actual')
            ->schema([
                Select::make('status')
                    ->options(['Operativo' => 'Operativo', 'Deficiente' => 'Deficiente', 'Retirado' => 'Retirado'])
                    ->required()
                    ->label('Estado'),

                
                ])
            ]);
    }
}
