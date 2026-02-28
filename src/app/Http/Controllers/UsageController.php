<?php

namespace App\Http\Controllers;

use App\Models\Mold;
use App\Models\UsageLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class UsageController extends Controller
{
    // 使用開始 lockForUpdate() による悲観的ロックで同時使用を防止
    public function start(Mold $mold): RedirectResponse
    {
        try {
            DB::transaction(function () use ($mold) {
                // 悲観的ロック（排他制御の核心）
                // 他トランザクションが同じ行を SELECT … FOR UPDATE している間はブロック
                $fresh = Mold::lockForUpdate()->findOrFail($mold->id);

                if ($fresh->status !== '待機中') {
                    throw new \RuntimeException(
                        'この金型は現在使用できません（状態: ' . $fresh->status . '）'
                    );
                }

                // ステータスを「使用中」に変更
                $fresh->update(['status' => '使用中']);

                // 使用ログを記録
                UsageLog::create([
                    'mold_id'    => $fresh->id,
                    'user_id'    => auth()->id(),
                    'start_time' => now(),
                ]);
            });

            return back()->with('success', '使用を開始しました。');

        } catch (\RuntimeException $e) {
            // ステータス不正など業務例外
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            // その他DB例外など
            return back()->with('error', '使用開始に失敗しました。もう一度お試しください。');
        }
    }

    // 使用終了 自分が開始した未終了のログを特定して終了処理
    public function end(Mold $mold): RedirectResponse
    {
        try {
            DB::transaction(function () use ($mold) {
                // 悲観的ロック
                $fresh = Mold::lockForUpdate()->findOrFail($mold->id);

                if ($fresh->status !== '使用中') {
                    throw new \RuntimeException(
                        'この金型は使用中ではありません（状態: ' . $fresh->status . '）'
                    );
                }

                // 自分が開始した未終了ログを取得
                $log = UsageLog::where('mold_id', $fresh->id)
                    ->where('user_id', auth()->id())
                    ->whereNull('end_time')
                    ->firstOrFail();

                $endTime = now();

                // 終了時刻・使用時間を記録
                $log->update([
                    'end_time'         => $endTime,
                    'duration_minutes' => (int) $log->start_time->diffInMinutes($endTime),
                ]);

                // 累計使用回数をインクリメント
                $fresh->increment('total_usage_count');

                // ステータスを「待機中」に戻す
                // ※ 承認済み予約が存在する場合は「予約済み」のままにする、はここで次の予約を確認させる機能を追加することが出来そう
                $fresh->update(['status' => '待機中']);
            });

            return back()->with('success', '使用を終了しました。');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'あなたの使用記録が見つかりません。');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            return back()->with('error', '使用終了に失敗しました。もう一度お試しください。');
        }
    }
}