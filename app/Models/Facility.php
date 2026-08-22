<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'country_id',
        'name',
        'type',
        'status',
        'building_condition',
        'efficiency_rating',
        'max_capacity',
        'purchase_cost',
        'monthly_operating_cost',
    ];

    protected $casts = [
        'building_condition' => 'integer',
        'efficiency_rating' => 'integer',
        'max_capacity' => 'integer',
        'purchase_cost' => 'decimal:4',
        'monthly_operating_cost' => 'decimal:4',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function productionLines(): HasMany
    {
        return $this->hasMany(ProductionLine::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
