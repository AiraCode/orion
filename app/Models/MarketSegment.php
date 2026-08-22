<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry_id',
        'name',
        'total_market_size_usd',
        'annual_growth_rate',
        'price_sensitivity',
        'quality_sensitivity',
    ];

    protected $casts = [
        'total_market_size_usd' => 'decimal:4',
        'annual_growth_rate' => 'decimal:4',
        'price_sensitivity' => 'integer',
        'quality_sensitivity' => 'integer',
    ];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
}
