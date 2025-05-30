<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Schedule;
use App\Models\Vehicle;

class Departure extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'schedule_id',
        'vehicle_id',
        'departure_time',
        'is_active'
    ];
    
    /**
     * Get the schedule that this departure belongs to
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
    
    /**
     * Get the vehicle assigned to this departure
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the tickets for this departure
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
