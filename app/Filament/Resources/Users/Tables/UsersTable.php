<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dni')
                    ->searchable()
                    ->sortable()
                    ->label('DNI'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),

                TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->default('Sin rol'),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Activo')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Creado')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Actualizado')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos los usuarios')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->indicator('Estado'),

                SelectFilter::make('has_roles')
                    ->label('Asignación de Roles')
                    ->options([
                        'with_roles' => 'Con roles asignados',
                        'without_roles' => 'Sin roles asignados',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'with_roles') {
                            return $query->whereHas('roles');
                        } elseif ($data['value'] === 'without_roles') {
                            return $query->whereDoesntHave('roles');
                        }
                        return $query;
                    })
                    ->indicator('Asignación'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver')
                        ->color('success'),

                    EditAction::make()
                        ->label('Editar'),

                    DeleteAction::make()
                        ->label('Eliminar')
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar Usuario')
                        ->modalDescription('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Sí, eliminar'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar Seleccionados')
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar Usuarios')
                        ->modalDescription('¿Estás seguro de que deseas eliminar los usuarios seleccionados?')
                        ->modalSubmitActionLabel('Sí, eliminar todos'),
                ])->label('Acciones en Lote'),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No hay usuarios registrados')
            ->emptyStateDescription('Comienza creando tu primer usuario del sistema.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
