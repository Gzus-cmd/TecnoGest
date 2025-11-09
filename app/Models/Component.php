<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Component extends Model
{
    protected $fillable = [
        'componentable_type',
        'componentable_id',
        'serial',
        'input_date',
        'output_date',
        'status',
        'warranty_months',
        'provider_id',
    ];

    public function componentable() : MorphTo
    {
        return $this->morphTo();
    }

    public function provider() : BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function computers() : MorphToMany
    {
        return $this->morphedByMany(Computer::class, 'componentables');
    }

    public function printers() : MorphToMany
    {
        return $this->morphedByMany(Printer::class, 'componentables');
    }
    
        // Component.php

    public function setComponentableTypeAttribute($value)
    {
        $this->attributes['componentable_type'] = match ($value) {
            'CPU' => \App\Models\CPU::class,
            'GPU' => \App\Models\GPU::class,
            'RAM' => \App\Models\RAM::class,
            default => $value,
        };
    }

    public function getComponentableTypeAttribute($value)
    {
        return match ($value) {
            \App\Models\CPU::class => 'CPU',
            \App\Models\GPU::class => 'GPU',
            \App\Models\RAM::class => 'RAM',
            default => class_basename($value),
        };
    }

}
