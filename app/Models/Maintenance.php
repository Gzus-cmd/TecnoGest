<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Maintenance extends Model
{
    
    protected $fillable = [
        'type',
        'deviceable_type',
        'deviceable_id',
        'registered_by',
        'status',
        'description',
        'requires_workshop',
        'workshop_location_id',
        'device_previous_status',
        'workshop_transfer_id',
        'updated_by',
    ];

    protected $casts = [
        'requires_workshop' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Al crear un mantenimiento, registrar quién lo creó
        static::creating(function (Maintenance $maintenance) {
            if (Auth::check() && !$maintenance->registered_by) {
                $maintenance->registered_by = Auth::id();
            }
            
            // Validar que el dispositivo esté Activo o Inactivo
            if ($maintenance->deviceable && 
                !in_array($maintenance->deviceable->status, ['Activo', 'Inactivo'])) {
                throw new \Exception('Solo se puede crear mantenimiento para dispositivos Activos o Inactivos');
            }
        });

        // Al actualizar un mantenimiento, registrar quién lo modificó
        static::updating(function (Maintenance $maintenance) {
            if (Auth::check()) {
                $maintenance->updated_by = Auth::id();
            }
            
            // Si cambió el status, guardar el estado anterior del dispositivo
            if ($maintenance->isDirty('status') && $maintenance->status === 'En Proceso') {
                $device = $maintenance->deviceable;
                if ($device && !$maintenance->device_previous_status) {
                    $maintenance->device_previous_status = $device->status;
                }
            }
        });

        // Después de actualizar, ejecutar lógica de cambio de estado
        static::updated(function (Maintenance $maintenance) {
            if ($maintenance->wasChanged('status')) {
                $maintenance->handleStatusChange();
            }
        });
    }

    /**
     * Maneja el cambio de estado del mantenimiento
     */
    protected function handleStatusChange(): void
    {
        $device = $this->deviceable;
        
        if (!$device) {
            return;
        }

        // Cuando el mantenimiento pasa a "En Proceso"
        if ($this->status === 'En Proceso') {
            // Cambiar dispositivo a "En Mantenimiento"
            $device->update(['status' => 'En Mantenimiento']);
            
            // Si requiere taller Y es una computadora, desasignar periféricos
            if ($this->requires_workshop && $device instanceof \App\Models\Computer) {
                if ($device->peripheral_id) {
                    $peripheral = $device->peripheral;
                    $peripheral->update([
                        'computer_id' => null,
                        'status' => 'Activo' // Queda disponible
                    ]);
                    $device->update(['peripheral_id' => null]);
                }
                
                // Crear traslado a taller
                $this->createWorkshopTransfer();
            } elseif ($this->requires_workshop) {
                // Si requiere taller pero NO es computadora, solo crear traslado
                $this->createWorkshopTransfer();
            }
        }

        // Cuando el mantenimiento es finalizado
        if ($this->status === 'Finalizado') {
            if ($this->requires_workshop) {
                // Si fue al taller, queda Inactivo hasta que regresen el dispositivo
                $device->update(['status' => 'Inactivo']);
            } else {
                // Si no fue al taller, restaurar estado anterior o Activo
                $newStatus = $this->device_previous_status ?? 'Activo';
                $device->update(['status' => $newStatus]);
            }
        }
    }

    /**
     * Crea un traslado al taller de informática
     */
    protected function createWorkshopTransfer(): void
    {
        $device = $this->deviceable;
        
        // Usar el workshop_location_id del mantenimiento o buscar un taller disponible
        $workshopLocationId = $this->workshop_location_id ?? 
                              \App\Models\Location::where('is_workshop', true)->first()?->id;
        
        if (!$device || !$workshopLocationId) {
            Log::warning("No se pudo crear traslado a taller para mantenimiento {$this->id}: dispositivo o taller no encontrado");
            return;
        }

        $transfer = Transfer::create([
            'deviceable_type' => $this->deviceable_type,
            'deviceable_id' => $this->deviceable_id,
            'registered_by' => $this->registered_by,
            'origin_id' => $device->location_id,
            'destiny_id' => $workshopLocationId,
            'date' => now(),
            'reason' => "Traslado a taller por mantenimiento {$this->type} - ID: {$this->id}",
            'status' => 'Finalizado',
        ]);

        // Actualizar sin disparar eventos para evitar recursión
        $this->updateQuietly(['workshop_transfer_id' => $transfer->id]);
    }

    public function deviceable() : MorphTo
    {
        return $this->morphTo();
    }

    public function registeredBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function updatedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function workshopTransfer() : BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'workshop_transfer_id');
    }
}
