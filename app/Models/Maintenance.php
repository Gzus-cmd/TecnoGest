<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    
    protected $fillable = [
        'type',
        'deviceable_type',
        'deviceable_id',
        'user_id',
        'status',
        'description',
    ];

    public function deviceable() : MorphTo
    {
        return $this->morphTo();
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
}
