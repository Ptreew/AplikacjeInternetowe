<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Stop;

class City extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'voivodeship',
    ];

    /**
     * Get all stops in this city
     */
    public function stops(): HasMany
    {
        return $this->hasMany(Stop::class);
    }
}
