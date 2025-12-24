<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OS extends Model
{
    use HasFactory;

    protected $table = 'o_s';

    protected $fillable = [
        'name',
        'version',
        'architecture',
    ];

    public function computers() : HasMany
    {
        return $this->hasMany(Computer::class);
    }
}
