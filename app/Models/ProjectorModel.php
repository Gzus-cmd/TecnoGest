<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectorModel extends Model
{
    
    protected $fillable = [
        'model',
        'resolution',
        'lumens',
        'vga',
        'hdmi',
    ];
}
