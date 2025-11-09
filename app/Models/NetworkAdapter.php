<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NetworkAdapter extends Model
{

    protected $table = 'network_adapters';
    
    protected $fillable = [
        'brand',
        'model',
        'mac_address',
        'connection_type',
        'speed_mbps',
    ];

    public function components() : MorphMany
    {
        return $this->morphMany(Component::class, 'componentable');
    }
}
