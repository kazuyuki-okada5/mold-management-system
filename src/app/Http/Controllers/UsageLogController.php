<?php

namespace App\Http\Controllers;

use App\Models\UsageLog;
use Illuminate\Http\Request;

class UsageLogController extends Controller
{
    /**
     * 使用履歴一覧
     * - admin: 全ユーザーの履歴を表示。使用者名・金型番号で検索可能
     * - operator: 自分の履歴のみ表示。金型番号で検索可能
     */
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = UsageLog::with(['mold', 'user'])
            ->latest('start_time');

        // operator は自分の履歴のみ
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // 検索フィルター
        if ($request->filled('mold_number')) {
            $query->whereHas('mold', function ($q) use ($request) {
                $q->where('mold_number', 'like', '%' . $request->mold_number . '%');
            });
        }

        // 使用者名フィルター（admin のみ有効）
        if ($user->role === 'admin' && $request->filled('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(30)->withQueryString();

        // サマリー（検索条件適用後の集計）
        $summaryQuery = UsageLog::query();

        if ($user->role !== 'admin') {
            $summaryQuery->where('user_id', $user->id);
        }

        if ($request->filled('mold_number')) {
            $summaryQuery->whereHas('mold', function ($q) use ($request) {
                $q->where('mold_number', 'like', '%' . $request->mold_number . '%');
            });
        }

        if ($user->role === 'admin' && $request->filled('user_name')) {
            $summaryQuery->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%');
            });
        }

        if ($request->filled('date_from')) {
            $summaryQuery->where('start_time', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->filled('date_to')) {
            $summaryQuery->where('start_time', '<=', $request->date_to . ' 23:59:59');
        }

        $summary = $summaryQuery->selectRaw(
            'COUNT(*) as total_count,
             COALESCE(SUM(duration_minutes), 0) as total_minutes'
        )->first();

        return view('usage-logs.index', compact('logs', 'summary'));
    }
}
