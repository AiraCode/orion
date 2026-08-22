<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'pay_date',
        'total_gross',
        'total_tax',
        'total_net',
        'employee_count',
        'status',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'total_gross' => 'decimal:4',
        'total_tax' => 'decimal:4',
        'total_net' => 'decimal:4',
        'employee_count' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
