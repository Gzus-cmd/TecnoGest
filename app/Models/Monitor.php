<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Monitor extends Model
{
    
    protected $fillable = [
        'brand',
        'model',
        'size',
        'resolution',
        'vga',
        'hdmi',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }
}
