<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'market_size',
        'growth_rate',
        'volatility',
    ];

    protected $casts = [
        'market_size' => 'decimal:4',
        'growth_rate' => 'decimal:4',
        'volatility' => 'decimal:4',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(Competitor::class);
    }

    public function marketSegments(): HasMany
    {
        return $this->hasMany(MarketSegment::class);
    }
}
