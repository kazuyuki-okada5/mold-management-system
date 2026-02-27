<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMoldRequest;
use App\Http\Requests\UpdateMoldRequest;
use App\Models\Mold;
use App\Models\UsageLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MoldController extends Controller
{
    // 金型登録フォーム
    public function create(): View
    {
        return view('molds.create');
    }

    // 金型登録処理
    public function store(StoreMoldRequest $request): RedirectResponse
    {
        Mold::create($request->validated());

        return redirect()
            ->route('molds.index')
            ->with('success', '金型「' . $request->name . '」を登録しました。');
    }

    // 金型編集フォーム
    public function edit(Mold $mold): View
    {
        return view('molds.edit', compact('mold'));
    }

    // 金型更新処理
    public function update(UpdateMoldRequest $request, Mold $mold): RedirectResponse
    {
        $mold->update($request->validated());

        return redirect()
            ->route('molds.show', $mold)
            ->with('success', '金型情報を更新しました。');
    }

    // 金型削除（ソフトデリート）
    public function destroy(Mold $mold): RedirectResponse
    {
        // 使用中は削除不可
        if ($mold->status === '使用中') {
            return back()->with('error', '使用中の金型は削除できません。');
        }

        // deleted_atに現在日時を記録（物理削除はしない）
        $mold->delete();

        return redirect()
            ->route('molds.index')
            ->with('success', '金型「' . $mold->name . '」を削除しました。');
    }

    // 金型稼働率・統計
    public function stats(): View
    {
        // 今月の使用回数を金型ごとに集計して、使用回数が多い順に上位10件を取得
        $monthlyStats = UsageLog::selectRaw('
                mold_id,
                COUNT(*) as usage_count,
                COALESCE(SUM(duration_minutes), 0) as total_minutes
            ')
            ->whereMonth('start_time', now()->month)
            ->whereYear('start_time', now()->year)
            ->with('mold')
            ->groupBy('mold_id')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get();

        // サマリー
        $summary = [
            'total_usage' => UsageLog::whereMonth('start_time', now()->month)->count(),
            'total_minutes' => UsageLog::whereMonth('start_time', now()->month)->sum('duration_minutes'),
            // 今月使用された金型の種類数（重複除外）
            'active_molds' => UsageLog::whereMonth('start_time', now()->month)->distinct('mold_id')->count('mold_id'),
            // 今月使用したユーザー数（重複除外）
            'active_users' => UsageLog::whereMonth('start_time', now()->month)->distinct('user_id')->count('user_id'),
        ];

        return view('admin.stats', compact('monthlyStats', 'summary'));
    }
}