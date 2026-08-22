<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_session_id',
        'industry_id',
        'country_id',
        'name',
        'ticker',
        'cash_balance',
        'credit_rating',
        'share_price',
        'total_shares',
        'valuation',
        'market_share_pct',
        'reputation_score',
        'founded_date',
    ];

    protected $casts = [
        'cash_balance' => 'decimal:4',
        'share_price' => 'decimal:4',
        'total_shares' => 'integer',
        'valuation' => 'decimal:4',
        'market_share_pct' => 'decimal:2',
        'reputation_score' => 'integer',
        'founded_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function financialSnapshots(): HasMany
    {
        return $this->hasMany(FinancialSnapshot::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function salesChannels(): HasMany
    {
        return $this->hasMany(SalesChannel::class);
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function marketingCampaigns(): HasMany
    {
        return $this->hasMany(MarketingCampaign::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function shareholders(): HasMany
    {
        return $this->hasMany(Shareholder::class);
    }

    public function rdProjects(): HasMany
    {
        return $this->hasMany(RdProject::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(GameAlert::class);
    }
}
