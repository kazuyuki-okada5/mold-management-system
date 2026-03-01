<x-layouts.app title="稼働率・統計" subtitle="今月の金型使用状況">

    <div class="space-y-6">

        {{-- サマリーカード（4列） --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">今月の総使用回数</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $summary['total_usage'] }}<span class="text-sm font-normal">回</span></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">📋</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">今月の総使用時間</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">
                            {{ intdiv($summary['total_minutes'], 60) }}<span class="text-sm font-normal">h</span>
                            {{ $summary['total_minutes'] % 60 }}<span class="text-sm font-normal">m</span>
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">⏱️</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">使用された金型数</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $summary['active_molds'] }}<span class="text-sm font-normal">台</span></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">🔩</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">アクティブユーザー</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $summary['active_users'] }}<span class="text-sm font-normal">名</span></p>
                        <p class="text-xs text-slate-400 mt-1">今月</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">👤</div>
                </div>
            </div>

        </div>

        {{-- 使用回数ランキング --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="font-bold text-slate-700 mb-4 text-sm">使用回数ランキング（今月）</h2>

            @if($monthlyStats->isEmpty())
                <p class="text-xs text-slate-400 text-center py-6">今月の使用データがありません</p>
            @else
                <div class="space-y-3">
                    @foreach($monthlyStats as $i => $item)
                        <div class="flex items-center gap-4">

                            {{-- 順位バッジ --}}
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
                                {{-- プログレスバー --}}
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full"
                                        style="width: {{ $monthlyStats->first()->usage_count > 0 ? round($item->usage_count / $monthlyStats->first()->usage_count * 100) : 0 }}%">
                                    </div>
                                </div>
                            </div>

                            <span class="text-xs text-slate-500 w-16 text-right">
                                {{ intdiv($item->total_minutes, 60) }}h{{ $item->total_minutes % 60 }}m
                            </span>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</x-layouts.app>