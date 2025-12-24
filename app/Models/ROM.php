<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ROM extends Model
{
    use HasFactory;
    
    protected $table = 'r_o_m_s';

    protected $fillable = [
        'brand',
        'model',
        'type',
        'capacity',
        'interface',
        'speed',
        'watts',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }
}
