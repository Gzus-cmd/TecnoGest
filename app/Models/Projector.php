<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Projector extends Model
{
    
    protected $fillable = [
        'modelo_id',
        'serial',
        'location_id',
        'status',
        'warranty_months',
        'input_date',
        'output_date',
    ];

    public function location() : BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function modelo() : BelongsTo
    {
        return $this->belongsTo(ProjectorModel::class, 'modelo_id');
    }

    public function components() : MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable', 'componentables')
            ->withPivot(['status', 'assigned_at'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    public function allComponents() : MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentable', 'componentables')
            ->withPivot(['status', 'assigned_at'])
            ->withTimestamps();
    }

    public function maintenances() : MorphMany
    {
        return $this->morphMany(Maintenance::class, 'deviceable');
    }

    public function transfers() : MorphMany 
    {
        return $this->morphMany(Transfer::class, 'deviceable');
    }

    protected static function booted(): void
    {
        // Eliminación en cascada
        static::deleting(function (Projector $projector) {
            // Eliminar mantenimientos
            $projector->maintenances()->delete();
            
            // Eliminar traslados
            $projector->transfers()->delete();
            
            // Desvincular componentes
            $projector->components()->detach();
        });
    }
}
