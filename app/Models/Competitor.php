<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Competitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry_id',
        'name',
        'market_share_pct',
        'brand_rating',
        'average_price_index',
    ];

    protected $casts = [
        'market_share_pct' => 'decimal:2',
        'brand_rating' => 'integer',
        'average_price_index' => 'decimal:2',
    ];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
}
