<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'contact_email',
        'lead_time_days',
        'quality_rating',
        'reliability_rating',
        'price_multiplier',
    ];

    protected $casts = [
        'lead_time_days' => 'integer',
        'quality_rating' => 'integer',
        'reliability_rating' => 'integer',
        'price_multiplier' => 'decimal:4',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'supplier_materials')
            ->withPivot('unit_price', 'min_order_quantity')
            ->withTimestamps();
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
