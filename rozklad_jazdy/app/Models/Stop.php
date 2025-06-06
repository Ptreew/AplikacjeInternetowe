<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\City;
use App\Models\RouteStop;
use App\Models\Route;

class Stop extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'city_id',
        'name',
        'code',
        'is_active'
    ];

    /**
     * Get the city that this stop belongs to
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the route stops for this stop
     */
    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    /**
     * Get the routes that use this stop
     */
    public function routes()
    {
        return $this->belongsToMany(Route::class, 'route_stops')
            ->withPivot('stop_number', 'distance_from_start', 'time_to_next');
    }
}
