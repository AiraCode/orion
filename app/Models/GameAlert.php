<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'game_session_id',
        'type', // info, success, warning, critical
        'title',
        'message',
        'category', // finance, operations, hr, market, system
        'is_read',
        'game_date',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'game_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }
}
