<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'product_id',
        'name',
        'daily_capacity_units',
        'current_utilization_pct',
        'efficiency_rating',
        'status',
    ];

    protected $casts = [
        'daily_capacity_units' => 'integer',
        'current_utilization_pct' => 'decimal:2',
        'efficiency_rating' => 'integer',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }
}
