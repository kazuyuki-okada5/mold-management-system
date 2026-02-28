<x-layouts.app>
    <x-slot name="title">管理ダッシュボード</x-slot>

    {{-- ページヘッダー --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">管理ダッシュボード</h1>
        <p class="text-sm text-slate-500 mt-0.5">全体の稼働状況</p>
    </div>

    <div class="space-y-6">

        {{-- サマリーカード（4列） --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            {{-- 総金型数 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">総金型数</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $moldStats->total }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">🔩</div>
                </div>
            </div>

            {{-- 待機中 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">待機中</p>
                        <p class="text-3xl font-bold text-emerald-700 mt-1">{{ $moldStats->standby }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">✅</div>
                </div>
            </div>

            {{-- 使用中 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">使用中</p>
                        <p class="text-3xl font-bold text-red-700 mt-1">{{ $moldStats->in_use }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl">🔴</div>
                </div>
            </div>

            {{-- 要メンテ --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">要メンテ</p>
                        <p class="text-3xl font-bold text-amber-700 mt-1">{{ $alerts->count() }}</p>
                        <p class="text-xs text-slate-400 mt-1">寿命80%超え</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">⚠️</div>
                </div>
            </div>
        </div>

        {{-- 今月集計バー --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div>
                <p class="text-xs text-blue-600 font-medium">今月の使用回数</p>
                <p class="text-2xl font-bold text-blue-900 mt-0.5">{{ $monthlyStats->total_count }}<span class="text-sm font-normal">回</span></p>
            </div>
            <div>
                <p class="text-xs text-blue-600 font-medium">今月の使用時間</p>
                <p class="text-2xl font-bold text-blue-900 mt-0.5">
                    {{ intdiv($monthlyStats->total_minutes, 60) }}<span class="text-sm font-normal">h</span>
                    {{ $monthlyStats->total_minutes % 60 }}<span class="text-sm font-normal">m</span>
                </p>
            </div>
            <div>
                <p class="text-xs text-blue-600 font-medium">使用された金型数</p>
                <p class="text-2xl font-bold text-blue-900 mt-0.5">{{ $monthlyStats->mold_count }}<span class="text-sm font-normal">台</span></p>
            </div>
            <div>
                <p class="text-xs text-blue-600 font-medium">アクティブユーザー</p>
                <p class="text-2xl font-bold text-blue-900 mt-0.5">{{ $monthlyStats->user_count }}<span class="text-sm font-normal">名</span></p>
            </div>
        </div>

        {{-- 2カラム：現在使用中 ＋ 承認待ち予約 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- 現在使用中 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <h2 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-2">
                    <span>🔴</span> 現在使用中
                    <span class="ml-auto bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $activeUsages->count() }}台
                    </span>
                </h2>
                @if($activeUsages->isEmpty())
                <p class="text-xs text-slate-400 text-center py-6">使用中の金型はありません</p>
                @else
                <div class="space-y-3">
                    @foreach($activeUsages as $usage)
                    <div class="flex items-center gap-3 p-2.5 bg-red-50 rounded-lg">
                        <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('molds.show', $usage->mold) }}"
                               class="font-mono text-xs font-bold text-red-700 hover:underline">
                                {{ $usage->mold->mold_number ?? '—' }}
                            </a>
                            <p class="text-xs text-slate-600 truncate">{{ $usage->mold->name ?? '—' }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $usage->user->name ?? '—' }} | {{ $usage->start_time->format('H:i') }}〜
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- 承認待ち予約 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 md:col-span-2">
                <h2 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-2">
                    <span>📋</span> 承認待ち予約
                    <span class="ml-auto bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $pendingReservations->count() }}件
                    </span>
                </h2>
                @if($pendingReservations->isEmpty())
                <p class="text-xs text-slate-400 text-center py-6">承認待ちの予約はありません</p>
                @else
                <div class="space-y-2">
                    @foreach($pendingReservations as $reservation)
                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg text-sm">
                        <div>
                            <p class="font-semibold text-slate-800">
                                {{ $reservation->user->name }} —
                                <a href="{{ route('molds.show', $reservation->mold) }}"
                                   class="font-mono text-blue-700 hover:underline">
                                    {{ $reservation->mold->mold_number }}
                                </a>
                                {{ $reservation->mold->name }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $reservation->reserved_start->format('m/d H:i') }}〜{{ $reservation->reserved_end->format('H:i') }}
                            </p>
                        </div>
                        <div class="flex gap-2 ml-3 flex-shrink-0">
                            {{-- 承認 --}}
                            <form action="{{ route('admin.reservations.approve', $reservation) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                                    承認
                                </button>
                            </form>
                            {{-- 否認 → 詳細画面へ --}}
                            <a href="{{ route('reservations.show', $reservation) }}"
                               class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">
                                否認
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- 寿命アラート --}}
        @if($alerts->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="font-bold text-slate-700 mb-4 text-sm">⚠️ 寿命アラート（要対応）</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($alerts as $mold)
                @php
                    $pct = $mold->max_usage_count > 0
                        ? round($mold->total_usage_count / $mold->max_usage_count * 100)
                        : 0;
                @endphp
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <a href="{{ route('molds.show', $mold) }}"
                           class="font-mono font-bold text-red-700 text-sm hover:underline">
                            {{ $mold->mold_number }}
                        </a>
                        <span class="text-xs font-bold {{ $pct >= 100 ? 'text-red-700' : 'text-amber-600' }}">{{ $pct }}%</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">{{ $mold->name }}</p>
                    <div class="w-full bg-red-200 rounded-full h-2 mt-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $mold->total_usage_count }} / {{ $mold->max_usage_count }} 回</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 今月の使用回数ランキング --}}
        @if($topMolds->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="font-bold text-slate-700 mb-4 text-sm">📈 今月の使用回数ランキング</h2>
            <div class="space-y-3">
                @foreach($topMolds as $i => $item)
                <div class="flex items-center gap-4">
                    {{-- 順位 --}}
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                        {{ $i === 0 ? 'bg-amber-400 text-white' : ($i === 1 ? 'bg-slate-400 text-white' : ($i === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold text-slate-700">
                                <a href="{{ route('molds.show', $item->mold) }}"
                                   class="font-mono text-blue-700 hover:underline">
                                    {{ $item->mold->mold_number ?? '—' }}
                                </a>
                                {{ $item->mold->name ?? '' }}
                            </span>
                            <span class="text-sm font-bold text-slate-800">{{ $item->usage_count }}回</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full"
                                style="width: {{ $topMolds->first()->usage_count > 0 ? round($item->usage_count / $topMolds->first()->usage_count * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <span class="text-xs text-slate-500 w-14 text-right">{{ $item->total_minutes ?? 0 }}分</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- /space-y-6 --}}
</x-layouts.app>