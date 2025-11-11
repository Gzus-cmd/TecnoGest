<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $fillable = [
        'deviceable_type',
        'deviceable_id',
        'user_id',
        'origin_id',
        'destiny_id',
        'date',
        'reason',
    ];

    protected static function booted(): void
    {
        // Cuando se crea un nuevo traslado
        static::created(function (Transfer $transfer) {
            $transfer->updateDeviceLocation();
        });

        // Cuando se actualiza un traslado existente
        static::updated(function (Transfer $transfer) {
            // Solo actualizar si cambió el destino
            if ($transfer->wasChanged('destiny_id')) {
                $transfer->updateDeviceLocation();
            }
        });
    }

    /**
     * Actualiza la ubicación del dispositivo al destino del traslado
     */
    protected function updateDeviceLocation(): void
    {
        $device = $this->deviceable;
        
        if ($device && $this->destiny_id) {
            $device->update(['location_id' => $this->destiny_id]);
        }
    }

    public function deviceable() : MorphTo
    {
        return $this->morphTo();
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
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
