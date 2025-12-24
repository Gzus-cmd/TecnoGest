<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CPU extends Model
{
    use HasFactory;
    protected $table = 'c_p_u_s';

    protected $fillable = [
        'brand',
        'model',
        'socket',
        'cores',
        'threads',
        'architecture',
        'watts',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }

}
