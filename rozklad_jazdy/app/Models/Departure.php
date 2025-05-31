<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Schedule;
use App\Models\Vehicle;
use Carbon\Carbon;

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
    
    /**
     * Get the stop that this departure is from
     */
    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }
    
    /**
     * Get the calculated arrival time based on departure time and route travel time
     * 
     * @return string
     */
    public function getArrivalTimeAttribute()
    {
        // Get the departure time as a Carbon instance
        $departureTime = Carbon::parse($this->departure_time);
        
        // Get the travel time from the route
        $travelTime = $this->schedule->route->travel_time ?? 0;
        
        // Add the travel time (in minutes) to the departure time
        $arrivalTime = $departureTime->copy()->addMinutes($travelTime);
        
        return $arrivalTime->format('H:i:s');
    }
}
