<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected string $view = 'filament.pages.my-profile';

    protected static ?string $title = 'Mi Perfil';

    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información Personal')
                    ->description('Tu información personal en el sistema')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('dni')
                                    ->label('DNI')
                                    ->disabled(),
                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->disabled(),
                            ]),
                    ]),

                Section::make('Información de Contacto')
                    ->description('Tu información de contacto')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->disabled(),
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->disabled(),
                            ]),
                    ]),

                Section::make('Cambiar Contraseña')
                    ->description('Completa estos campos para cambiar tu contraseña')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Contraseña Actual')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule('current_password:web')
                            ->validationMessages([
                                'current_password' => 'La contraseña actual no es correcta.',
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Nueva Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->minLength(8)
                                    ->same('password_confirmation')
                                    ->validationMessages([
                                        'same' => 'Las contraseñas no coinciden.',
                                        'required' => 'Debes ingresar una nueva contraseña.',
                                        'min' => 'La contraseña debe tener al menos 8 caracteres.',
                                    ]),
                                TextInput::make('password_confirmation')
                                    ->label('Confirmar Nueva Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->required(),
                            ]),
                    ])
                    ->footerActions([
                        Action::make('changePassword')
                            ->label('Cambiar Contraseña')
                            ->action('changePassword')
                            ->requiresConfirmation()
                            ->modalHeading('¿Cambiar contraseña?')
                            ->modalDescription('¿Estás seguro de que deseas cambiar tu contraseña?')
                            ->color('primary'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Roles y Permisos')
                    ->description('Tus roles y permisos en el sistema (solo lectura)')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('roles_display')
                                    ->label('Roles Asignados')
                                    ->content(fn () => Auth::user()->roles->pluck('name')->implode(', ') ?: 'Sin roles'),
                                Placeholder::make('permissions_count')
                                    ->label('Total de Permisos')
                                    ->content(fn () => Auth::user()->getAllPermissions()->count() . ' permisos'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function changePassword(): void
    {
        try {
            $data = $this->form->getState();

            $user = Auth::user();

            // Actualizar solo la contraseña
            $user->update([
                'password' => Hash::make($data['password']),
            ]);

            // Limpiar campos de contraseña
            $this->form->fill([
                'dni' => $user->dni,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'current_password' => '',
                'password' => '',
                'password_confirmation' => '',
            ]);

            Notification::make()
                ->success()
                ->title('Contraseña actualizada')
                ->body('Tu contraseña ha sido cambiada correctamente.')
                ->send();

        } catch (Halt $exception) {
            return;
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error al cambiar contraseña')
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->send();
        }
    }
}
