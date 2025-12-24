<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Información Básica
                Section::make('Información Personal')
                    ->description('Datos personales del usuario')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('dni')
                                    ->label('DNI')
                                    ->required()
                                    ->maxLength(8)
                                    ->placeholder('12345678'),
                                
                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Juan Pérez García'),
                            ]),
                    ]),

                Section::make('Información de Contacto')
                    ->description('Correo electrónico y teléfono')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('usuario@empresa.com'),
                                
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->maxLength(9)
                                    ->placeholder('999888777'),
                            ]),
                    ]),

                Section::make('Seguridad y Estado')
                    ->description('Contraseña de acceso y estado del usuario')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->placeholder('Mínimo 8 caracteres')
                                    ->helperText(fn (string $context): string => $context === 'edit' ? 'Dejar vacío para no cambiar la contraseña' : 'Mínimo 8 caracteres'),
                                
                                Toggle::make('is_active')
                                    ->label('Usuario Activo')
                                    ->helperText('Indica si el usuario sigue trabajando en la empresa')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),

                Section::make('Asignación de Roles')
                    ->description('Roles que determinan los permisos del usuario')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->options(Role::all()->pluck('name', 'id'))
                            ->helperText('Selecciona uno o más roles'),

                        Placeholder::make('roles_info')
                            ->label('Información de Roles')
                            ->content(function ($record) {
                                if (!$record) {
                                    return 'Guarda el usuario para ver información de roles';
                                }

                                $roles = $record->roles;
                                if ($roles->isEmpty()) {
                                    return '❌ Sin roles asignados';
                                }

                                $roleNames = $roles->pluck('name')->implode(', ');
                                $totalPermissions = $record->getAllPermissions()->count();

                                return "✅ Roles: {$roleNames}\n📊 Total de permisos: {$totalPermissions}";
                            })
                            ->hiddenOn('create'),
                    ]),

                Section::make('Permisos Directos')
                    ->description('Permisos adicionales específicos (opcional)')
                    ->icon('heroicon-o-key')
                    ->schema(
                        self::getPermissionSections()
                    )
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(true)
                    ->hidden(fn ($record) => $record && $record->hasRole('super_admin')),
            ]);
    }

    protected static function getPermissionSections(): array
    {
        $permissions = Permission::all();
        $groupedPermissions = [];

        // Agrupar permisos por modelo
        foreach ($permissions as $permission) {
            $parts = explode(':', $permission->name);
            $model = count($parts) > 1 ? $parts[1] : 'Otros';
            
            if (!isset($groupedPermissions[$model])) {
                $groupedPermissions[$model] = [];
            }
            $groupedPermissions[$model][] = $permission;
        }

        // Ordenar modelos alfabéticamente
        ksort($groupedPermissions);

        $sections = [];

        // Crear una sección por cada modelo
        foreach ($groupedPermissions as $model => $perms) {
            $options = [];
            foreach ($perms as $perm) {
                $options[$perm->id] = $perm->name;
            }

            $sections[] = Section::make($model)
                ->description("Permisos relacionados con {$model}")
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('')
                        ->relationship('permissions', 'name')
                        ->options($options)
                        ->columns(2)
                        ->gridDirection('row')
                        ->bulkToggleable(),
                ])
                ->collapsible()
                ->collapsed(true)
                ->compact();
        }

        if (empty($sections)) {
            $sections[] = Placeholder::make('no_permissions')
                ->label('')
                ->content('No hay permisos disponibles para asignar');
        }

        return $sections;
    }
}
