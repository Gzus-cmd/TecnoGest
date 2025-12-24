<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class GPU extends Model
{
    use HasFactory;
    
    protected $table = 'g_p_u_s';

    protected $fillable = [
        'brand',
        'model',
        'memory',
        'capacity',
        'interface',
        'frequency',
        'watts',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }
}
