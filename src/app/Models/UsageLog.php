<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'mold_id',
        'user_id',
        'start_time',
        'end_time',
        'duration_minutes',
    ];

    // start_time を Carbon インスタンスとして扱うために必要(UsageController@end で $log->start_time->diffInMinutes() を呼ぶ)
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