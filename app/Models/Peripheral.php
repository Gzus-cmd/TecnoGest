<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Peripheral extends Model
{
    protected $fillable = [
        'code',
        'location_id',
        'computer_id',
        'notes',
    ];

    protected static function booted(): void
    {
        // Validación al asignar a una computadora
        static::updating(function (Peripheral $peripheral) {
            if ($peripheral->isDirty('computer_id') && $peripheral->computer_id) {
                $computer = Computer::find($peripheral->computer_id);
                
                if (!$computer) {
                    throw new \Exception('La computadora seleccionada no existe');
                }
                
                if ($computer->peripheral_id && $computer->peripheral_id !== $peripheral->id) {
                    throw new \Exception('La computadora ya tiene un periférico asignado');
                }
            }
        });
        
        // Sincronización bidireccional Peripheral ↔ Computer
        static::updated(function (Peripheral $peripheral) {
            if ($peripheral->wasChanged('computer_id')) {
                // Asignar la computadora nueva
                if ($peripheral->computer_id) {
                    $computer = Computer::find($peripheral->computer_id);
                    if ($computer && $computer->peripheral_id !== $peripheral->id) {
                        $computer->updateQuietly(['peripheral_id' => $peripheral->id]);
                    }
                }
                
                // Liberar la computadora anterior
                $oldComputerId = $peripheral->getOriginal('computer_id');
                if ($oldComputerId && $oldComputerId !== $peripheral->computer_id) {
                    $oldComputer = Computer::find($oldComputerId);
                    if ($oldComputer && $oldComputer->peripheral_id === $peripheral->id) {
                        $oldComputer->updateQuietly(['peripheral_id' => null]);
                    }
                }
            }
        });

        // Eliminación en cascada
        static::deleting(function (Peripheral $peripheral) {
            // Desvincular componentes
            $peripheral->components()->detach();
        });
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class);
    }

    public function components(): MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    public function allComponents(): MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }

    public function removedComponents(): MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Removido')
            ->orderByPivot('assigned_at', 'desc');
    }

    public function dismantledComponents(): MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Desmantelado')
            ->orderByPivot('assigned_at', 'desc');
    }

    public function transfers(): MorphMany
    {
        return $this->morphMany(Transfer::class, 'deviceable');
    }

    public function maintenances(): MorphMany
    {
        return $this->morphMany(Maintenance::class, 'deviceable');
    }

    // Métodos helper para obtener componentes específicos
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

    /**
     * Desmantela todos los componentes vigentes de este conjunto de periféricos
     */
    public function dismantleAllComponents(): void
    {
        $this->components()->updateExistingPivot(
            $this->components->pluck('id'),
            ['status' => 'Desmantelado']
        );
    }
}
