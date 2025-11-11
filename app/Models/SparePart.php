<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SparePart extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'description',
        'part_number',
        'type',
        'specifications',
    ];

    protected $casts = [
        'specifications' => 'array',
    ];

    /**
     * Obtener todas las instancias (componentes) de este repuesto
     */
    public function components(): MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }

    /**
     * Obtener solo las instancias disponibles
     */
    public function availableComponents(): MorphMany
    {
        return $this->morphMany(Component::class, 'componentable')
            ->where('status', 'Operativo')
            ->doesntHave('computers')
            ->doesntHave('printers')
            ->doesntHave('projectors');
    }
}
