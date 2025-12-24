<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar')
                ->requiresConfirmation()
                ->modalHeading('Eliminar Usuario')
                ->modalDescription('¿Estás seguro? Esta acción eliminará también sus roles y permisos asignados.')
                ->modalSubmitActionLabel('Sí, eliminar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario actualizado')
            ->body('Los cambios, incluyendo roles y permisos, han sido guardados correctamente.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si la contraseña está vacía, no actualizarla
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Limpiar caché de permisos para este usuario
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
