<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'route_id',
        'day_of_week',
        'is_active'
    ];
    
    /**
     * Get the route that this schedule belongs to
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
    
    /**
     * Get the departures for this schedule
     */
    public function departures(): HasMany
    {
        return $this->hasMany(Departure::class);
    }
}
