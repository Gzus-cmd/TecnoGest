<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dni',
        'name',
        'email',
        'phone',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Eventos del modelo User
     * Asigna rol predeterminado cuando se crea un nuevo usuario
     */
    protected static function booted(): void
    {
        parent::booted();

        static::created(function (User $user) {
            // Asignar rol predeterminado a nuevos usuarios
            if (!$user->hasAnyRole()) {
                $user->assignRole('panel_user');
            }
        });
    }

    /**
     * Verificar si el usuario es super administrador
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Verificar si el usuario puede acceder al panel de Filament
     */
    public function canAccessPanel(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin',
            'tecnico',
            'panel_user',
            'viewer'
        ]);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function maintenances() : HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
