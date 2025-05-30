<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Line;

class FavouriteLine extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'line_id'
    ];
    
    /**
     * Get the user that saved this favourite line
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the line that was saved as favourite
     */
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }
}
