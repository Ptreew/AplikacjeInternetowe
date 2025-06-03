<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_id',
        'name',
        'travel_time',
        'is_active'
    ];

    /**
     * Get the line that owns the route
     */
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    /**
     * Get the route stops for this route
     */
    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    /**
     * Get the stops for this route via route_stops
     */
    public function stops()
    {
        return $this->belongsToMany(Stop::class, 'route_stops')
            ->withPivot('stop_number', 'distance_from_start', 'time_to_next')
            ->orderBy('stop_number');
    }

    /**
     * Get the schedules for this route
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
