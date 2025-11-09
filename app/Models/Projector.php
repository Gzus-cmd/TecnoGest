<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Projector extends Model
{
    
    protected $fillable = [
        'modelo_id',
        'serial',
        'location_id',
        'warranty_months',
        'input_date',
        'output_date',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }

    
}
