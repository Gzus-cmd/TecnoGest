<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Auth;

class Component extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'componentable_type',
        'componentable_id',
        'serial',
        'input_date',
        'output_date',
        'status',
        'warranty_months',
        'provider_id',
        'registered_by',
        'retired_by',
    ];

    protected $casts = [
        'input_date' => 'date',
        'output_date' => 'date',
    ];

    protected static function booted(): void
    {
        // Al crear un componente, registrar quién lo creó
        static::creating(function (Component $component) {
            if (Auth::check() && !$component->registered_by) {
                $component->registered_by = Auth::id();
            }
        });

        // Al cambiar el estado a "Retirado", registrar quién lo retiró
        static::updating(function (Component $component) {
            if ($component->isDirty('status') && $component->status === 'Retirado') {
                if (Auth::check() && !$component->retired_by) {
                    $component->retired_by = Auth::id();
                    $component->output_date = now()->toDateString();
                }
            }
        });
    }

    public function componentable() : MorphTo
    {
        return $this->morphTo();
    }

    public function provider() : BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function registeredBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function retiredBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'retired_by');
    }

    public function computers() : MorphToMany
    {
        return $this->morphedByMany(Computer::class, 'componentable', 'componentables')
            ->withPivot(['assigned_at', 'status', 'assigned_by', 'removed_by'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    public function printers() : MorphToMany
    {
        return $this->morphedByMany(Printer::class, 'componentable', 'componentables')
            ->withPivot(['assigned_at', 'status', 'assigned_by', 'removed_by'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    public function projectors() : MorphToMany
    {
        return $this->morphedByMany(Projector::class, 'componentable', 'componentables')
            ->withPivot(['assigned_at', 'status', 'assigned_by', 'removed_by'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    public function peripheral() : MorphToMany
    {
        return $this->morphedByMany(Peripheral::class, 'componentable', 'componentables')
            ->withPivot(['assigned_at', 'status', 'assigned_by', 'removed_by'])
            ->withTimestamps()
            ->wherePivot('status', 'Vigente');
    }

    // Historial completo de asignaciones
    public function allAssignments() : MorphToMany
    {
        // Unión de todos los dispositivos (Computer, Printer, Projector)
        return $this->morphedByMany(Computer::class, 'componentable', 'componentables')
            ->withPivot(['assigned_at', 'status', 'componentable_type', 'assigned_by', 'removed_by'])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }

    public function assignmentHistory()
    {
        $computers = $this->morphedByMany(Computer::class, 'componentable', 'componentables')
            ->withPivot(['assigned_at', 'status', 'assigned_by', 'removed_by'])
            ->withTimestamps()
            ->get()
            ->map(function ($device) {
                return [
                    'device_type' => 'Computadora',
                    'device_serial' => $device->serial,
                    'device_location' => $device->location->name ?? 'N/A',
                    'assigned_at' => $device->pivot->assigned_at,
                    'status' => $device->pivot->status,
                    'assigned_by' => $device->pivot->assigned_by,
                    'removed_by' => $device->pivot->removed_by,
                ];
            });

        $printers = $this->morphedByMany(Printer::class, 'componentable', 'componentables')
            ->withPivot(['assigned_at', 'status', 'assigned_by', 'removed_by'])
            ->withTimestamps()
            ->get()
            ->map(function ($device) {
                return [
                    'device_type' => 'Impresora',
                    'device_serial' => $device->serial,
                    'device_location' => $device->location->name ?? 'N/A',
                    'assigned_at' => $device->pivot->assigned_at,
                    'status' => $device->pivot->status,
                    'assigned_by' => $device->pivot->assigned_by,
                    'removed_by' => $device->pivot->removed_by,
                ];
            });

        $projectors = $this->morphedByMany(Projector::class, 'componentable', 'componentables')
            ->withPivot(['assigned_at', 'status', 'assigned_by', 'removed_by'])
            ->withTimestamps()
            ->get()
            ->map(function ($device) {
                return [
                    'device_type' => 'Proyector',
                    'device_serial' => $device->serial,
                    'device_location' => $device->location->name ?? 'N/A',
                    'assigned_at' => $device->pivot->assigned_at,
                    'status' => $device->pivot->status,
                    'assigned_by' => $device->pivot->assigned_by,
                    'removed_by' => $device->pivot->removed_by,
                ];
            });

        return $computers->concat($printers)->concat($projectors)->sortByDesc('assigned_at');
    }
}
