<?php

namespace App\Http\Controllers;

use App\Models\Mold;
use App\Models\UsageLog;
use Illuminate\Http\Request;

class MoldController extends Controller
{
    // 金型一覧（検索・フィルタ対応）
    public function index(Request $request)
    {
        $molds = Mold::query()
            // 検索フォームに入力があった場合だけ条件追加。無ければ全件取得
            ->when($request->filled('mold_number'), function ($q) use ($request) {
                $q->where('mold_number', 'like', '%' . $request->mold_number . '%');
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('warehouse'), function ($q) use ($request) {
                $q->where('warehouse', $request->warehouse);
            })
            ->when($request->filled('floor'), function ($q) use ($request) {
                $q->where('floor', $request->floor);
            })
            // N+1対策：現在使用中のログとそのユーザー情報をまとめて取得
            ->with(['activeUsage.user'])
            ->orderBy('mold_number')
            ->paginate(20)
            // 検索条件維持
            ->withQueryString();

        // 寿命80%超えアラート対象
        $alerts = Mold::nearingLimit()->get();

        // 検索フォーム用の選択肢
        $warehouses = Mold::whereNotNull('warehouse')->distinct()->pluck('warehouse')->sort()->values();
        $floors     = Mold::whereNotNull('floor')->distinct()->pluck('floor')->sort()->values();

        return view('molds.index', compact('molds', 'alerts', 'warehouses', 'floors'));
    }

    // 金型詳細
    public function show(Mold $mold)
    {
        // 取得した後に関連データを追加で読み込む
        $mold->load([
            'usageLogs' => fn ($q) => $q->with('user')->latest('start_time')->limit(10),
            // 予約状況：今後の承認待ち・承認済みのみユーザー情報込みで取得
            'reservations' => fn ($q) => $q
                ->with('user')
                ->where('reserved_end', '>=', now())
                ->whereIn('status', ['pending', 'approved'])
                ->orderBy('reserved_start'),
            'activeUsage.user',
        ]);

        // 集計（該当金型の使用統計を1回のSQLで取得）
        $stats = UsageLog::where('mold_id', $mold->id)
            ->selectRaw('
                COUNT(*) as total_count,
                COALESCE(SUM(duration_minutes), 0) as total_minutes,
                COUNT(CASE WHEN MONTH(start_time) = MONTH(NOW())
                    AND YEAR(start_time) = YEAR(NOW()) THEN 1 END) as this_month_count,
                COALESCE(SUM(CASE WHEN MONTH(start_time) = MONTH(NOW())
                    AND YEAR(start_time) = YEAR(NOW()) THEN duration_minutes END), 0) as this_month_minutes
            ')
            ->first();
        // compact() で、変数を配列にまとめてビューに渡す
        return view('molds.show', compact('mold', 'stats'));
    }
}