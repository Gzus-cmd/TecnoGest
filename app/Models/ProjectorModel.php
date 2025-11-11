<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectorModel extends Model
{
    
    protected $fillable = [
        'model',
        'resolution',
        'lumens',
        'vga',
        'hdmi',
    ];

    public function projectors() : HasMany {
        return $this->hasMany(Projector::class);
    }
}
