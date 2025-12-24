<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SparePart extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'description',
        'part_number',
        'type',
        'specifications',
    ];

    protected function casts(): array
    {
        return [
            'specifications' => 'array',
        ];
    }

    public function components(): MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }
}
