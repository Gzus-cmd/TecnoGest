<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OS extends Model
{
    protected $table = 'o_s';

    protected $fillable = [
        'name',
        'version',
        'license_key',
        'architecture',
    ];

    public function computers() : HasMany
    {
        return $this->hasMany(Computer::class);
    }
}
