<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static ?string $title = 'Registrar Usuario';

    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario creado exitosamente')
            ->body('El usuario ha sido registrado y sus roles han sido asignados correctamente.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asegurar que la contraseña esté hasheada
        if (isset($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Los roles y permisos se sincronizan automáticamente por la relación
        // Pero podemos agregar lógica adicional aquí si es necesario
        
        $user = $this->record;
        
        // Si no tiene roles asignados, asignar rol predeterminado
        if (!$user->roles()->exists()) {
            $user->assignRole('panel_user');
            
            Notification::make()
                ->info()
                ->title('Rol predeterminado asignado')
                ->body('Se asignó automáticamente el rol "panel_user" al usuario.')
                ->send();
        }
    }
}
