<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Transfer extends Model
{
    protected $fillable = [
        'deviceable_type',
        'deviceable_id',
        'registered_by',
        'origin_id',
        'destiny_id',
        'date',
        'reason',
        'status',
        'updated_by',
    ];

    protected static function booted(): void
    {
        // Al crear un traslado, registrar quién lo creó y establecer el origen
        static::creating(function (Transfer $transfer) {
            if (Auth::check() && !$transfer->registered_by) {
                $transfer->registered_by = Auth::id();
            }
            
            // Validar que el dispositivo no esté desmantelado
            if ($transfer->deviceable && $transfer->deviceable->status === Status::DEVICE_DISMANTLED) {
                throw new \Exception('No se puede trasladar un dispositivo desmantelado');
            }
            
            // Establecer automáticamente el origen como la ubicación actual del dispositivo
            if (!$transfer->origin_id && $transfer->deviceable) {
                $transfer->origin_id = $transfer->deviceable->location_id;
            }
        });

        // Cuando se crea un nuevo traslado
        static::created(function (Transfer $transfer) {
            $transfer->updateDeviceLocation();
        });

        // Cuando se actualiza un traslado existente
        static::updating(function (Transfer $transfer) {
            if (Auth::check()) {
                $transfer->updated_by = Auth::id();
            }
        });
        
        // Después de actualizar un traslado
        static::updated(function (Transfer $transfer) {
            // Actualizar ubicación si cambió el destino o el estado
            if ($transfer->wasChanged('destiny_id') || $transfer->wasChanged('status')) {
                $transfer->updateDeviceLocation();
            }
        });
    }

    /**
     * Actualiza la ubicación del dispositivo al destino del traslado
     * Solo cuando el estado es 'Finalizado'
     */
    protected function updateDeviceLocation(): void
    {
        $device = $this->deviceable;
        
        if ($device && $this->destiny_id && 
            $this->status === Status::TRANSFER_COMPLETED && 
            $this->wasChanged('status')) {
            $device->update(['location_id' => $this->destiny_id]);
            
            // Si el dispositivo está Inactivo y sale de un taller
            if ($device->status === Status::DEVICE_INACTIVE) {
                $origin = $this->origin;
                
                // Verificar si el origen es un taller/área de informática
                if ($origin && $origin->is_workshop) {
                    $device->update(['status' => Status::DEVICE_ACTIVE]);
                }
            }
        }
    }

    public function deviceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function origin() : BelongsTo    
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destiny() : BelongsTo
    {
        return $this->belongsTo(Location::class, 'destiny_id');
    }
}
