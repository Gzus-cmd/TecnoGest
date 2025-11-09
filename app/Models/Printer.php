<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Printer extends Model
{
    protected $fillable = [
        'printerModel_id',
        'serial',
        'location_id',
        'ip_address',
        'status',
        'warranty_months',
        'input_date',
        'output_date',
    ];

    public function location() : BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function printerModel() : BelongsTo
    {
        return $this->belongsTo(PrinterModel::class);
    }

    public function components() : MorphToMany
    {
        return $this->morphToMany(Component::class, 'componentables');
    }

    public function maintenances() : MorphMany
    {
        return $this->morphMany(Maintenance::class, 'deviceable');
    }

    public function transfers() : MorphMany 
    {
        return $this->morphMany(Transfer::class, 'deviceable');
    }
}
