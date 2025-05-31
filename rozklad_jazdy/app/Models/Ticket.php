<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Departure;

class Ticket extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'departure_id',
        'ticket_number',
        'price',
        'status',
        'purchase_date',
        'passenger_name',
        'passenger_email',
        'passenger_phone',
        'notes',
        'is_active'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'datetime',
        'price' => 'decimal:2'
    ];
    
    /**
     * Get the user that purchased this ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the departure this ticket is for
     */
    public function departure(): BelongsTo
    {
        return $this->belongsTo(Departure::class);
    }
}
