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

    protected static function booted(): void
    {
        // Eliminación en cascada
        static::deleting(function (ProjectorModel $model) {
            // Eliminar proyectores asociados (disparando sus eventos deleting)
            $model->projectors->each->delete();
        });
    }
}
