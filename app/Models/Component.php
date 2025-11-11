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
        return $this->morphedByMany(Computer::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    public function printers() : MorphToMany
    {
        return $this->morphedByMany(Printer::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    public function projectors() : MorphToMany
    {
        return $this->morphedByMany(Projector::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    // Historial completo de asignaciones
    public function allAssignments() : MorphToMany
    {
        // Unión de todos los dispositivos (Computer, Printer, Projector)
        return $this->morphedByMany(Computer::class, 'componentable')
            ->withPivot(['assigned_at', 'status', 'componentable_type'])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }

    public function assignmentHistory()
    {
        $computers = $this->morphedByMany(Computer::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->get()
            ->map(function ($device) {
                return [
                    'device_type' => 'Computadora',
                    'device_serial' => $device->serial,
                    'device_location' => $device->location->name ?? 'N/A',
                    'assigned_at' => $device->pivot->assigned_at,
                    'status' => $device->pivot->status,
                ];
            });

        $printers = $this->morphedByMany(Printer::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->get()
            ->map(function ($device) {
                return [
                    'device_type' => 'Impresora',
                    'device_serial' => $device->serial,
                    'device_location' => $device->location->name ?? 'N/A',
                    'assigned_at' => $device->pivot->assigned_at,
                    'status' => $device->pivot->status,
                ];
            });

        $projectors = $this->morphedByMany(Projector::class, 'componentable')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps()
            ->get()
            ->map(function ($device) {
                return [
                    'device_type' => 'Proyector',
                    'device_serial' => $device->serial,
                    'device_location' => $device->location->name ?? 'N/A',
                    'assigned_at' => $device->pivot->assigned_at,
                    'status' => $device->pivot->status,
                ];
            });

        return $computers->concat($printers)->concat($projectors)->sortByDesc('assigned_at');
    }
}
