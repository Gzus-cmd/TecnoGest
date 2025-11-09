<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Motherboard extends Model
{
    
    protected $fillable = [
        'brand',
        'model',
        'socket',
        'chipset',
        'format',
        'slots_ram',
        'ports_sata',
        'ports_m2',
        'watts',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }
}
