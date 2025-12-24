<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AudioDevice extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'type',
        'speakers',
    ];


    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }


}
