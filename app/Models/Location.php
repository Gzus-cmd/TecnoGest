<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'pavilion',
        'apartment',
        'is_workshop'
    ];

    /**
     * Traslados donde esta ubicación es el ORIGEN
     */
    public function originTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'origin_id');
    }

    /**
     * Traslados donde esta ubicación es el DESTINO
     */
    public function destinyTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'destiny_id');
    }

    /**
     * Mantenimientos realizados en esta ubicación (si es taller)
     */
    public function workshopMaintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class, 'workshop_location_id');
    }

    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    public function printers(): HasMany
    {
        return $this->hasMany(Printer::class);
    }

    public function projectors(): HasMany
    {
        return $this->hasMany(Projector::class);
    }

    public function peripherals(): HasMany
    {
        return $this->hasMany(Peripheral::class);
    }
}
