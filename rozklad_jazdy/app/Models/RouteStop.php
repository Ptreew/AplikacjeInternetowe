<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStop extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'route_id',
        'stop_id',
        'stop_number',
        'distance_from_start',
        'time_to_next'
    ];
    
    /**
     * Get the route that this route stop belongs to
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
    
    /**
     * Get the stop that this route stop belongs to
     */
    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }
}
