<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type',
        'name',
        'principal_amount',
        'current_value',
        'annual_return_rate',
        'purchase_date',
        'maturity_date',
        'status',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:4',
        'current_value' => 'decimal:4',
        'annual_return_rate' => 'decimal:4',
        'purchase_date' => 'date',
        'maturity_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
