<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    
    protected $fillable = [
        'name',
        'pavilion',
        'apartment'
    ];

    public function transfers() : HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function maintenances() : HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function computers() : HasMany
    {
        return $this->hasMany(Computer::class);
    }

    public function printers() : HasMany
    {
        return $this->hasMany(Printer::class);
    }
}
