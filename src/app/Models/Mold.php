<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mold extends Model
{
    use HasFactory, SoftDeletes;

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
        'total_usage_count' => 'integer',
        'max_usage_count'   => 'integer',
    ];

    // ========== リレーション ==========
    // 金型予約一覧
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // 金型使用履歴一覧
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
            ->where('max_usage_count', '>', 0)
            ->whereRaw('total_usage_count >= max_usage_count * 0.8');
    }

    // 寿命超過（100%以上）
    public function scopeOverLimit($query)
    {
        return $query
            ->whereNotNull('max_usage_count')
            ->where('max_usage_count', '>', 0)
            ->whereRaw('total_usage_count >= max_usage_count');
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

    // 寿命アラートが必要か（80％以上）
    public function getNearingLimitAttribute(): bool
    {
        return $this->max_usage_count
            && $this->total_usage_count >= $this->max_usage_count * 0.8;
    }
    
    // ステータスに応じた Tailwind バッジクラス
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            '待機中' => 'bg-emerald-100 text-emerald-700',
            '使用中' => 'bg-red-100 text-red-700',
            '予約済み' => 'bg-amber-100 text-amber-700',
            'メンテナンス中' => 'bg-slate-100 text-slate-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    // ステータスに応じたドット色クラス
    public function getStatusDotColorAttribute(): string
    {
        return match ($this->status) {
            '待機中' => 'bg-emerald-500',
            '使用中' => 'bg-red-500',
            '予約済み' => 'bg-amber-500',
            'メンテナンス中' => 'bg-slate-400',
            default => 'bg-gray-400',
        };
    }

    // ========== ヘルパー ==========
    
    // 使用可能か（待機中のみ）
    public function isAvailable(): bool
    {
        return $this->status === '待機中';
    }
        
        // 現在使用中か
    public function isInUse(): bool
    {
        return $this->status === '使用中';
    }
}