<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TowerCase extends Model
{

    protected $table = 'tower_cases';
    
    protected $fillable = [
        'brand',
        'model',
        'format',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }
}
