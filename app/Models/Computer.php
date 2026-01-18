<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Computer extends Model
{
    use HasFactory;

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
        // Validación al asignar un periférico
        static::updating(function (Computer $computer) {
            if ($computer->isDirty('peripheral_id') && $computer->peripheral_id) {
                $peripheral = Peripheral::find($computer->peripheral_id);

                if (!$peripheral) {
                    throw new \Exception('El periférico seleccionado no existe');
                }

                if ($peripheral->computer_id && $peripheral->computer_id !== $computer->id) {
                    throw new \Exception('El periférico ya está asignado a otra computadora');
                }
            }
        });

        // Sincronización bidireccional Computer ↔ Peripheral
        static::updated(function (Computer $computer) {
            if ($computer->wasChanged('peripheral_id')) {
                // Asignar el periférico nuevo
                if ($computer->peripheral_id) {
                    $peripheral = Peripheral::find($computer->peripheral_id);
                    if ($peripheral && $peripheral->computer_id !== $computer->id) {
                        $peripheral->updateQuietly(['computer_id' => $computer->id]);
                    }
                }

                // Liberar el periférico anterior
                $oldPeripheralId = $computer->getOriginal('peripheral_id');
                if ($oldPeripheralId && $oldPeripheralId !== $computer->peripheral_id) {
                    $oldPeripheral = Peripheral::find($oldPeripheralId);
                    if ($oldPeripheral && $oldPeripheral->computer_id === $computer->id) {
                        $oldPeripheral->updateQuietly(['computer_id' => null]);
                    }
                }
            }
        });

        // Eliminación en cascada
        static::deleting(function (Computer $computer) {
            // Eliminar mantenimientos
            $computer->maintenances()->delete();

            // Eliminar traslados
            $computer->transfers()->delete();

            // Desvincular componentes (eliminar de tabla pivote)
            $computer->components()->detach();
        });
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function peripheral(): BelongsTo
    {
        return $this->belongsTo(Peripheral::class);
    }

    public function os(): BelongsTo
    {
        return $this->belongsTo(OS::class, 'os_id');
    }

    public function components(): MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente')
            ->whereNotIn('components.componentable_type', [
                'Monitor',
                'Keyboard',
                'Mouse',
                'AudioDevice',
                'Stabilizer',
                'Splitter',
            ]); // Solo componentes internos (excluir periféricos)
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

    // Métodos helper para obtener componentes específicos
    public function motherboards()
    {
        return $this->components()->where('components.componentable_type', 'Motherboard');
    }

    public function cpus()
    {
        return $this->components()->where('components.componentable_type', 'CPU');
    }

    public function gpus()
    {
        return $this->components()->where('components.componentable_type', 'GPU');
    }

    public function rams()
    {
        return $this->components()->where('components.componentable_type', 'RAM');
    }

    public function roms()
    {
        return $this->components()->where('components.componentable_type', 'ROM');
    }

    public function monitors()
    {
        return $this->components()->where('components.componentable_type', 'Monitor');
    }

    public function keyboards()
    {
        return $this->components()->where('components.componentable_type', 'Keyboard');
    }

    public function mice()
    {
        return $this->components()->where('components.componentable_type', 'Mouse');
    }

    public function networkAdapters()
    {
        return $this->components()->where('components.componentable_type', 'NetworkAdapter');
    }

    public function powerSupplies()
    {
        return $this->components()->where('components.componentable_type', 'PowerSupply');
    }

    public function towerCases()
    {
        return $this->components()->where('components.componentable_type', 'TowerCase');
    }

    public function audioDevices()
    {
        return $this->components()->where('components.componentable_type', 'AudioDevice');
    }

    public function stabilizers()
    {
        return $this->components()->where('components.componentable_type', 'Stabilizer');
    }

    public function splitters()
    {
        return $this->components()->where('components.componentable_type', 'Splitter');
    }

    public function maintenances(): MorphMany
    {
        return $this->morphMany(Maintenance::class, 'deviceable');
    }

    public function transfers()
    {
        return $this->morphMany(Transfer::class, 'deviceable');
    }
}
