<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RdProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'category',
        'daily_budget',
        'total_budget',
        'spent_amount',
        'progress_pct',
        'start_date',
        'estimated_completion_date',
        'status',
        'unlocked_features',
    ];

    protected $casts = [
        'daily_budget' => 'decimal:4',
        'total_budget' => 'decimal:4',
        'spent_amount' => 'decimal:4',
        'progress_pct' => 'decimal:2',
        'start_date' => 'date',
        'estimated_completion_date' => 'date',
        'unlocked_features' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
