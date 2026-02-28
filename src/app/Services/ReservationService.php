<?php

namespace App\Services;

use App\Models\Mold;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * 指定期間に予約の重複があるか確認する
     *
     * @param int       $moldId     対象金型ID
     * @param Carbon    $start      予約開始日時
     * @param Carbon    $end        予約終了日時
     * @param int|null  $excludeId  更新時は自分自身を除外するID
     * @return bool  true = 重複あり
     */
    public function checkOverlap(
        int $moldId,
        Carbon $start,
        Carbon $end,
        ?int $excludeId = null
    ): bool {
        return Reservation::where('mold_id', $moldId)
            ->whereIn('status', ['pending', 'approved'])
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($start, $end) {
                // 既存予約の開始が新規予約の範囲内に入る
                $q->whereBetween('reserved_start', [$start, $end])
                  // 既存予約の終了が新規予約の範囲内に入る
                  ->orWhereBetween('reserved_end', [$start, $end])
                  // 既存予約が新規予約を完全に包含する
                  ->orWhere(function ($q) use ($start, $end) {
                      $q->where('reserved_start', '<=', $start)
                        ->where('reserved_end', '>=', $end);
                  });
            })
            ->exists();
    }

    // 予約を承認する(金型ステータスを「予約済み」に更新)
    public function approve(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // 金型ステータスを予約済みに変更
            // （既に使用中やメンテ中の場合は上書きしない）
            $reservation->mold->update(['status' => '予約済み']);
        });
    }

    // 予約を否認する
    public function reject(Reservation $reservation, string $reason): void
    {
        $reservation->update([
            'status'        => 'rejected',
            'reject_reason' => $reason,
        ]);
    }

    // 予約をキャンセルする（申請者本人 or 管理者が実行）
    // キャンセル後、その金型に他の承認済み予約がなければ待機中に戻す
    public function cancel(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'cancelled']);

            // 他の承認済み予約が存在しない場合は金型ステータスを待機中に戻す
            $hasOtherApproved = Reservation::where('mold_id', $reservation->mold_id)
                ->where('id', '!=', $reservation->id)
                ->where('status', 'approved')
                ->where('reserved_end', '>=', now())
                ->exists();

            if (! $hasOtherApproved) {
                $mold = $reservation->mold;
                // 使用中でなければ待機中に戻す
                if ($mold->status === '予約済み') {
                    $mold->update(['status' => '待機中']);
                }
            }
        });
    }

    // 予約開始時刻を過ぎた承認済み予約を自動的に完了扱いにする（Scheduled Command から呼び出す想定）
    public function completeExpired(): int
    {
        return Reservation::where('status', 'approved')
            ->where('reserved_end', '<', now())
            ->update(['status' => 'completed']);
    }
}