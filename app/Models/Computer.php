<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Computer extends Model
{
    
    protected $fillable = [
        'serial',
        'location_id',
        'status',
        'ip_address',
        'os_id',
        'peripheral_id',
    ];

    protected static function booted(): void
    {
        // El desmantelamiento ahora se maneja manualmente si es necesario
        // Los estados disponibles son solo: Activo e Inactivo
    }

    /**
     * Desmantela todos los componentes vigentes de esta computadora
     * Solo desmantela componentes INTERNOS (CPU, GPU, RAM, etc.)
     * NO desmantela periféricos (Monitor, Teclado, Mouse, etc.)
     */
    public function dismantleAllComponents(): void
    {
        $this->components()->updateExistingPivot(
            $this->components->pluck('id'),
            ['status' => 'Desmantelado']
        );
    }

    public function location() : BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function peripheral() : BelongsTo
    {
        return $this->belongsTo(Peripheral::class);
    }

    public function os() : BelongsTo
    {
        return $this->belongsTo(OS::class, 'os_id');
    }

    public function components() : MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente')
            ->whereNotIn('components.componentable_type', [
                'App\Models\Monitor',
                'App\Models\Keyboard',
                'App\Models\Mouse',
                'App\Models\AudioDevice',
                'App\Models\Stabilizer',
                'App\Models\Splitter',
            ]); // Solo componentes internos (excluir periféricos)
    }

    public function allComponents() : MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }

    public function removedComponents() : MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Removido')
            ->orderByPivot('assigned_at', 'desc');
    }

    public function dismantledComponents() : MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Desmantelado')
            ->orderByPivot('assigned_at', 'desc');
    }

    // Métodos helper para obtener componentes específicos
    public function motherboards()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\Motherboard');
    }

    public function cpus()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\CPU');
    }

    public function gpus()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\GPU');
    }

    public function rams()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\RAM');
    }

    public function roms()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\ROM');
    }

    public function monitors()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\Monitor');
    }

    public function keyboards()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\Keyboard');
    }

    public function mice()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\Mouse');
    }

    public function networkAdapters()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\NetworkAdapter');
    }

    public function powerSupplies()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\PowerSupply');
    }

    public function towerCases()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\TowerCase');
    }

    public function audioDevices()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\AudioDevice');
    }

    public function stabilizers()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\Stabilizer');
    }

    public function splitters()
    {
        return $this->components()->where('components.componentable_type', 'App\Models\Splitter');
    }

    public function maintenances() : MorphMany
    {
        return $this->morphMany(Maintenance::class, 'deviceable');
    }

    public function transfers()
    {
        return $this->morphMany(Transfer::class, 'deviceable');
    }
    

}
