<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'production_line_id',
        'product_id',
        'quantity_ordered',
        'quantity_produced',
        'status',
        'start_date',
        'completion_date',
        'total_production_cost',
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_produced' => 'integer',
        'start_date' => 'date',
        'completion_date' => 'date',
        'total_production_cost' => 'decimal:4',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function productionLine(): BelongsTo
    {
        return $this->belongsTo(ProductionLine::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
