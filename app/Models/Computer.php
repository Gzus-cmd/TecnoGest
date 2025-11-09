<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Computer extends Model
{
    
    protected $fillable = [
        'serial',
        'location_id',
        'status',
        'ip_address',
        'os_id',
    ];

    public function location() : BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function os() : BelongsTo
    {
        return $this->belongsTo(OS::class, 'os_id');
    }

    public function components() : MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentables');
    }

    public function maintenances() : MorphMany
    {
        return $this->morphMany(Maintenance::class, 'deviceable');
    }

    public function transfers()
    {
        return $this->morphMany(Transfer::class, 'deviceable');
    }
    

}
