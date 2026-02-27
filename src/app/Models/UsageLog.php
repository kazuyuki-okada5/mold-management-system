<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageLog extends Model
{
    protected $fillable = [
        'mold_id',
        'user_id',
        'start_time',
        'end_time',
        'duration_minutes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    // ========== リレーション ==========

    public function mold(): BelongsTo
    {
        return $this->belongsTo(Mold::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ========== スコープ ==========

    /** 使用中（終了していない） */
    public function scopeInProgress($query)
    {
        return $query->whereNull('end_time');
    }

    /** 今月のログ */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('start_time', now()->month)
                     ->whereYear('start_time', now()->year);
    }
}