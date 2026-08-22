<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'location',
        'max_capacity_units',
        'current_capacity_units',
        'monthly_lease_cost',
    ];

    protected $casts = [
        'max_capacity_units' => 'integer',
        'current_capacity_units' => 'integer',
        'monthly_lease_cost' => 'decimal:4',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }
}
