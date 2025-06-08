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
        'days_of_week',
        'valid_from',
        'valid_to'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'days_of_week' => 'array',
        'valid_from' => 'date',
        'valid_to' => 'date'
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
