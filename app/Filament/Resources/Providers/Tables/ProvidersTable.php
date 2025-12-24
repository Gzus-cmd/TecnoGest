<?php

namespace App\Filament\Resources\Providers\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProvidersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ruc')
                    ->label('RUC')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Contacto')
                    ->icon('heroicon-m-envelope')
                    ->description(function ($record) {
                        $parts = [];
                        if ($record->phone) {
                            $parts[] = '📞 ' . $record->phone;
                        }
                        if ($record->address) {
                            $parts[] = '📍 ' . $record->address;
                        }
                        return implode("\n", $parts);
                    })
                    ->searchable(['email', 'phone', 'address'])
                    ->copyable()
                    ->copyMessage('Email copiado')
                    ->wrap(),
                IconColumn::make('status')
                    ->label('Estado')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver'),
                    EditAction::make()
                        ->label('Editar'),
                    DeleteAction::make()
                        ->label('Eliminar'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('primary')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay ningún registro de proveedores');
    }
}
