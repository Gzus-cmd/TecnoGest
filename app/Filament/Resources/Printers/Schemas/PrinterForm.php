<?php

namespace App\Filament\Resources\Printers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class PrinterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                
                
                Section::make()
                ->schema([
                Grid::make(2)
                ->schema
                ([
                Select::make('modelo_id')
                    ->relationship('printerModel','model')
                    ->label('Modelo')
                    ->required(),
                TextInput::make('serial')
                    ->required()
                    ->label('Número de Serie')])

                    ,
                Grid::make(2)
                ->schema
                    ([Select::make('location_id')
                    ->label('Departamento')
                    ->relationship('location', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "Pabellón: {$record->pavilion} | {$record->name}")
                    ->required(),
                    Select::make('status')
                        ->label('Estado')
                        ->options([
                'Activo' => 'Activo',
                'Inactivo' => 'Inactivo',
                'En Mantenimiento' => 'En mantenimiento',
                'Desmantelado' => 'Desmantelado',
            ])
                        ->required()])
                        ])
                        ,
                Section::make()
                ->schema([
                
                Grid::make(2)->schema
                    ([TextInput::make('ip_address')
                    ->label('Dirección IP'),
                TextInput::make('warranty_months')
                        ->numeric()
                        ->label('Meses de Garantía')])
                    ,
                Grid::make(2)->schema
                ([DatePicker::make('input_date')
                    ->label('Fecha de Entrada')
                    ->required(),
                DatePicker::make('output_date')
                    ->label('Fecha de Salida')])
                
                ]),
                


            ]);
    }
}
