<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'unit',
        'base_cost',
        'quality_score',
        'description',
    ];

    protected $casts = [
        'base_cost' => 'decimal:4',
        'quality_score' => 'integer',
    ];

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'supplier_materials')
            ->withPivot('unit_price', 'min_order_quantity')
            ->withTimestamps();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_bills_of_materials')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }
}
