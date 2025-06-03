<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Carrier;
use App\Models\Departure;

class Vehicle extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'carrier_id',
        'type',
        'number',
        'capacity',
        'is_active'
    ];
    
    /**
     * Get the carrier that owns this vehicle
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }
    
    /**
     * Get the departures that this vehicle is assigned to
     */
    public function departures(): HasMany
    {
        return $this->hasMany(Departure::class);
    }
    
    /**
     * Get the line that this vehicle is assigned to
     */
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }
}
