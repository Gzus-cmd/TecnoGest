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
        return $this->hasMany(Printer::class);
    }

    protected static function booted(): void
    {
        // Eliminación en cascada
        static::deleting(function (PrinterModel $model) {
            // Eliminar impresoras asociadas (disparando sus eventos deleting)
            $model->printers->each->delete();
        });
    }
}
