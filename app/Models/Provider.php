<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'name',
        'phone',
        'email',
        'address',
        'status',
    ];

    public function components() : HasMany
    {
        return $this->hasMany(Component::class);
    }
}
