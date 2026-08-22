<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'game_date',
        'period_type',
        'revenue',
        'cogs',
        'gross_profit',
        'operating_expenses',
        'net_profit',
        'cash_balance',
        'total_assets',
        'total_liabilities',
        'total_equity',
        'valuation',
    ];

    protected $casts = [
        'game_date' => 'date',
        'revenue' => 'decimal:4',
        'cogs' => 'decimal:4',
        'gross_profit' => 'decimal:4',
        'operating_expenses' => 'decimal:4',
        'net_profit' => 'decimal:4',
        'cash_balance' => 'decimal:4',
        'total_assets' => 'decimal:4',
        'total_liabilities' => 'decimal:4',
        'total_equity' => 'decimal:4',
        'valuation' => 'decimal:4',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
