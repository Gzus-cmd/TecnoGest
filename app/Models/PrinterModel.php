<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrinterModel extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'type',
        'color',
        'scanner',
        'wifi',
        'ethernet',
    ];

    public function printers() : HasMany
    {
        return $this->hasMany(Component::class);
    }
}
