<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RAM extends Model
{
    use HasFactory;
    
    protected $table = 'r_a_m_s';    

    protected $fillable = [
        'brand',
        'model',
        'type',
        'technology',
        'capacity',
        'frequency',
        'watts',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }
}
