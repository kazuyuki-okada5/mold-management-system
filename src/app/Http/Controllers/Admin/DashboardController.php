<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mold;
use App\Models\Reservation;
use App\Models\UsageLog;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function index()
    {
        // 金型ステータス別件数
        $moldStats = Mold::query()
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = '待機中') as standby,
                SUM(status = '使用中') as in_use,
                SUM(status = '予約済み') as reserved,
                SUM(status = 'メンテナンス中') as maintenance
            ")
            ->first();

        // 寿命アラート（累計使用回数が寿命の80%以上の金型を、使用率の高い順に最大5件取得）
        $alerts = Mold::whereNotNull('max_usage_count')
            ->whereRaw('total_usage_count >= max_usage_count * 0.8')
            ->orderByRaw('total_usage_count / max_usage_count DESC')
            ->limit(5)
            ->get();

        // 承認待ち予約（予約開始日時が早い順に最大10件取得）
        $pendingReservations = Reservation::with(['mold', 'user'])
            ->where('status', 'pending')
            ->orderBy('reserved_start')
            ->limit(10)
            ->get();

        // 現在使用中の金型（使用開始が新しい順に取得）
        $activeUsages = UsageLog::with(['mold', 'user'])
            ->whereNull('end_time')
            ->latest('start_time')
            ->get();

        // 今月の集計（使用回数・合計時間・使用された金型数・アクティブユーザー数を1回のSQLで取得）
        $monthlyStats = UsageLog::selectRaw("
            COUNT(*) AS total_count,
            COALESCE(SUM(duration_minutes), 0) AS total_minutes,
            COUNT(DISTINCT mold_id) AS mold_count,
            COUNT(DISTINCT user_id) AS user_count
        ")
        ->whereYear('start_time', now()->year)
        ->whereMonth('start_time', now()->month)
        ->first();

        // 今月の使用回数ランキング（上位5）
        $topMolds = UsageLog::with('mold')
            ->selectRaw('mold_id, COUNT(*) as usage_count, SUM(duration_minutes) as total_minutes')
            ->whereYear('start_time', now()->year)
            ->whereMonth('start_time', now()->month)
            ->groupBy('mold_id')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'moldStats',
            'alerts',
            'pendingReservations',
            'activeUsages',
            'monthlyStats',
            'topMolds',
        ));
    }
}
