<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'mold_id',
        'user_id',
        'approved_by',
        'reserved_start',
        'reserved_end',
        'status',
        'purpose',
        'reject_reason',
        'approved_at',
    ];

    protected $casts = [
        'reserved_start' => 'datetime',
        'reserved_end'   => 'datetime',
        'approved_at'    => 'datetime',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ========== スコープ ==========

    // 承認待ちのみ
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // 有効な予約（pending or approved）
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }

    // 指定金型の将来の予約（詳細画面用）
    public function scopeUpcoming($query)
    {
        return $query->where('reserved_end', '>=', now());
    }
}