<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shareholder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'share_count',
        'ownership_percentage',
        'total_invested',
        'voting_power_pct',
    ];

    protected $casts = [
        'share_count' => 'integer',
        'ownership_percentage' => 'decimal:2',
        'total_invested' => 'decimal:4',
        'voting_power_pct' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
