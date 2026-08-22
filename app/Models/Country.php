<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'currency_code',
        'tax_rate',
        'labor_cost_index',
        'logistics_index',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:4',
        'labor_cost_index' => 'decimal:4',
        'logistics_index' => 'decimal:4',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
