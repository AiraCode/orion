<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'product_id',
        'name',
        'channel',
        'daily_budget',
        'total_spend',
        'start_date',
        'end_date',
        'reach',
        'leads_generated',
        'status',
    ];

    protected $casts = [
        'daily_budget' => 'decimal:4',
        'total_spend' => 'decimal:4',
        'start_date' => 'date',
        'end_date' => 'date',
        'reach' => 'integer',
        'leads_generated' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
