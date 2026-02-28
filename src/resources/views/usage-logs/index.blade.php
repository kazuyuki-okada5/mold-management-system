<x-layouts.app>
    <x-slot name="title">使用履歴</x-slot>

    {{-- ページヘッダー --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">使用履歴</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ auth()->user()->role === 'admin' ? '全ユーザーの金型使用記録' : '自分の金型使用記録' }}
        </p>
    </div>
    <div class="space-y-4">

        {{-- 検索フォーム --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <form method="GET" action="{{ route('usage-logs.index') }}">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">

                    {{-- 金型番号 --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">金型番号</label>
                        <input
                            type="text"
                            name="mold_number"
                            value="{{ request('mold_number') }}"
                            placeholder="M-001"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        >
                    </div>

                    {{-- 使用者名（admin のみ） --}}
                    @if(auth()->user()->role === 'admin')
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">使用者名</label>
                        <input
                            type="text"
                            name="user_name"
                            value="{{ request('user_name') }}"
                            placeholder="田中"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        >
                    </div>
                    @else
                    {{-- operator 用の空スペース（グリッドを崩さないため） --}}
                    <div></div>
                    @endif

                    {{-- 期間（開始） --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">期間（開始）</label>
                        <input
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        >
                    </div>

                    {{-- 期間（終了） --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">期間（終了）</label>
                        <input
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        >
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-3">
                    <a
                        href="{{ route('usage-logs.index') }}"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 transition-colors"
                    >
                        リセット
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors"
                    >
                        検索
                    </button>
                </div>
            </form>
        </div>

        {{-- サマリーバー（検索結果の集計） --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="text-blue-500 text-sm">📋</span>
                <span class="text-sm text-blue-800">
                    該当件数：<strong>{{ $logs->total() }}件</strong>
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-blue-500 text-sm">⏱️</span>
                <span class="text-sm text-blue-800">
                    合計使用時間：<strong>
                        {{ intdiv($summary->total_minutes, 60) }}時間
                        {{ $summary->total_minutes % 60 }}分
                    </strong>
                </span>
            </div>
        </div>

        {{-- テーブル --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

            {{-- テーブルヘッダー --}}
            <div class="px-5 py-4 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-700">
                    {{ $logs->total() }}件中
                    {{ $logs->firstItem() ?? 0 }}〜{{ $logs->lastItem() ?? 0 }}件を表示
                </p>
            </div>
            @if($logs->isEmpty())
            <div class="px-5 py-12 text-center text-slate-400 text-sm">
                該当する使用履歴がありません
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">金型</th>
                            @if(auth()->user()->role === 'admin')
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">使用者</th>
                            @endif
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">開始日時</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">終了日時</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">使用時間</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">

                            {{-- 金型 --}}
                            <td class="px-4 py-3">
                                @if($log->mold)
                                <a href="{{ route('molds.show', $log->mold) }}"
                                   class="font-mono font-bold text-blue-700 text-xs hover:underline">
                                    {{ $log->mold->mold_number }}
                                </a>
                                <p class="text-xs text-slate-600 mt-0.5">{{ $log->mold->name }}</p>
                                @else
                                <span class="text-xs text-slate-400">（削除済み）</span>
                                @endif
                            </td>

                            {{-- 使用者（adminのみ） --}}
                            @if(auth()->user()->role === 'admin')
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center text-xs font-bold text-blue-700 flex-shrink-0">
                                        {{ mb_substr($log->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-slate-700">{{ $log->user->name ?? '（削除済み）' }}</span>
                                </div>
                            </td>
                            @endif

                            {{-- 開始日時 --}}
                            <td class="px-4 py-3 text-slate-600 text-xs">
                                {{ $log->start_time->format('Y/m/d H:i') }}
                            </td>

                            {{-- 終了日時 --}}
                            <td class="px-4 py-3 text-xs">
                                @if($log->end_time)
                                    <span class="text-slate-600">{{ $log->end_time->format('Y/m/d H:i') }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        使用中
                                    </span>
                                @endif
                            </td>

                            {{-- 使用時間 --}}
                            <td class="px-4 py-3">
                                @if($log->duration_minutes !== null)
                                    <span class="font-bold text-blue-700">{{ $log->duration_minutes }}分</span>
                                    <span class="text-xs text-slate-400 ml-1">
                                        ({{ intdiv($log->duration_minutes, 60) }}h{{ $log->duration_minutes % 60 }}m)
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ページネーション --}}
            @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    ページ {{ $logs->currentPage() }} / {{ $logs->lastPage() }}
                </p>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
            @endif

            @endif {{-- isEmpty --}}
        </div>{{-- /テーブルカード --}}

    </div>{{-- /space-y-4 --}}
</x-layouts.app>