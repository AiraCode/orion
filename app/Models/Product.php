<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'sku',
        'category',
        'description',
        'selling_price',
        'target_unit_cost',
        'quality_rating',
        'appeal_score',
        'status',
    ];

    protected $casts = [
        'selling_price' => 'decimal:4',
        'target_unit_cost' => 'decimal:4',
        'quality_rating' => 'integer',
        'appeal_score' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'product_bills_of_materials')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    public function productionLines(): HasMany
    {
        return $this->hasMany(ProductionLine::class);
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }
}
