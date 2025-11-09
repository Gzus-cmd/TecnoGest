<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $fillable = [
        'deviceable_type',
        'deviceable_id',
        'user_id',
        'origin_id',
        'destiny_id',
        'date',
        'reason',
    ];


    public function deviceable() : MorphTo
    {
        return $this->morphTo();
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function origin() : BelongsTo    
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destiny() : BelongsTo
    {
        return $this->belongsTo(Location::class, 'destiny_id');
    }

    
}
