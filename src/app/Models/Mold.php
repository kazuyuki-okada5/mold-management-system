<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mold extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mold_number',
        'name',
        'specifications',
        'manufacture_date',
        'status',
        'warehouse',
        'floor',
        'shelf_number',
        'total_usage_count',
        'max_usage_count',
    ];

    protected $casts = [
        'manufacture_date' => 'date',
    ];

    // ========== リレーション ==========

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(UsageLog::class);
    }

    // 現在進行中の使用ログ（end_timeがnull）
    public function activeUsage(): HasOne
    {
        return $this->hasOne(UsageLog::class)->whereNull('end_time');
    }

    // ========== スコープ ==========

    // ステータス絞り込み
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // 寿命80%以上（アラート対象）
    public function scopeNearingLimit($query)
    {
        return $query
            ->whereNotNull('max_usage_count')
            ->whereRaw('total_usage_count >= max_usage_count * 0.8');
    }

    // ========== アクセサ ==========

    // 寿命使用率（%）を返す。max未設定はnull（算出出来る為、カラムには追加せず常に最新の値にしたいのでDBには持たせない）
    public function getUsageRateAttribute(): ?float
    {
        if (!$this->max_usage_count) {
            return null;
        }
        return round($this->total_usage_count / $this->max_usage_count * 100, 1);
    }
}