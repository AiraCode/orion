<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'department_id',
        'facility_id',
        'first_name',
        'last_name',
        'role_title',
        'salary',
        'skill_level',
        'morale',
        'productivity',
        'hired_date',
        'status',
    ];

    protected $casts = [
        'salary' => 'decimal:4',
        'skill_level' => 'integer',
        'morale' => 'integer',
        'productivity' => 'integer',
        'hired_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
