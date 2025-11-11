<?php

namespace App\Filament\Resources\NetworkAdapters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NetworkAdapterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del adaptador de red')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Intel, Realtek, Broadcom'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('I225, RTL8111, BCM5751'),
                            ]),
                    ]),

                Section::make('Especificaciones Técnicas')
                    ->description('Tipo e interfaz de conexión')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('type')
                                    ->label('Tipo')
                                    ->required()
                                    ->placeholder('Ethernet, WiFi, Bluetooth'),
                                TextInput::make('interface')
                                    ->label('Interfaz')
                                    ->required()
                                    ->placeholder('PCIe, USB, M.2'),
                            ]),
                    ]),

                Section::make('Rendimiento y Consumo')
                    ->description('Velocidad y potencia del adaptador')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('speed')
                                    ->label('Velocidad (Mbps)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('100, 1000, 10000'),
                                TextInput::make('watts')
                                    ->label('Vatios')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('0.5, 1, 2'),
                            ]),
                    ]),
            ]);
    }
}
