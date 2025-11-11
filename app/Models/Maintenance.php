<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    
    protected $fillable = [
        'type',
        'deviceable_type',
        'deviceable_id',
        'user_id',
        'status',
        'description',
        'requires_workshop',
        'device_previous_status',
        'workshop_transfer_id',
    ];

    protected $casts = [
        'requires_workshop' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Cuando se actualiza un mantenimiento
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

        // Cuando el mantenimiento pasa a "En Progreso"
        if ($this->status === 'En Progreso') {
            // Guardar el estado anterior del dispositivo
            if (!$this->device_previous_status) {
                $this->update(['device_previous_status' => $device->status]);
            }
            
            // Cambiar dispositivo a "En Mantenimiento"
            $device->update(['status' => 'En Mantenimiento']);
            
            // Si requiere taller, crear traslado a Informática
            if ($this->requires_workshop && !$this->workshop_transfer_id) {
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
     * Crea un traslado al área de Informática
     */
    protected function createWorkshopTransfer(): void
    {
        $device = $this->deviceable;
        $informaticaLocation = \App\Models\Location::where('name', 'Sala de Informática')->first();
        
        if (!$device || !$informaticaLocation) {
            return;
        }

        $transfer = Transfer::create([
            'deviceable_type' => $this->deviceable_type,
            'deviceable_id' => $this->deviceable_id,
            'user_id' => $this->user_id,
            'origin_id' => $device->location_id,
            'destiny_id' => $informaticaLocation->id,
            'date' => now(),
            'reason' => "Traslado a taller por mantenimiento {$this->type} - ID: {$this->id}",
        ]);

        $this->update(['workshop_transfer_id' => $transfer->id]);
    }

    public function deviceable() : MorphTo
    {
        return $this->morphTo();
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workshopTransfer() : BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'workshop_transfer_id');
    }
}
