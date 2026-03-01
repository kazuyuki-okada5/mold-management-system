<x-layouts.app title="ダッシュボード" subtitle="今日の状況をご確認ください">

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
                        <p class="text-xs text-slate-400 mt-1">使用可能</p>
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

            {{-- 私の予約 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">私の予約</p>
                        <p class="text-3xl font-bold text-amber-700 mt-1">{{ $myReservationCount }}</p>
                        <p class="text-xs text-slate-400 mt-1">今後の予約</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">📅</div>
                </div>
            </div>
        </div>

        {{-- 2カラム：直近の予約 ＋ 最近の使用履歴 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- 私の予約（直近） --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <h2 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-2">
                    <span>📅</span> 私の予約（直近）
                </h2>
                @if($myReservations->isEmpty())
                    <p class="text-xs text-slate-400 text-center py-6">予約はありません</p>
                @else
                    <div class="space-y-3">
                        @foreach($myReservations as $r)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-slate-800 text-xs">
                                        {{ $r->mold->mold_number }} {{ $r->mold->name }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $r->reserved_start->format('Y/m/d H:i') }}
                                        〜
                                        {{ $r->reserved_end->format('H:i') }}
                                    </p>
                                </div>
                                <x-reservation-status-badge :status="$r->status" />
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('reservations.index') }}"
                        class="text-xs text-blue-600 hover:underline font-semibold">
                        すべての予約を見る →
                    </a>
                </div>
            </div>

            {{-- 最近の使用履歴 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <h2 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-2">
                    <span>📋</span> 最近の使用履歴
                </h2>
                @if($myLogs->isEmpty())
                    <p class="text-xs text-slate-400 text-center py-6">使用履歴がありません</p>
                @else
                    <div class="space-y-3">
                        @foreach($myLogs as $log)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-slate-800 text-xs">
                                        {{ $log->mold->mold_number }} {{ $log->mold->name }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $log->start_time->format('Y/m/d') }}
                                    </p>
                                </div>
                                <span class="text-xs text-slate-600 font-semibold bg-white border border-slate-200 px-2 py-1 rounded">
                                    {{ $log->duration_minutes }}分
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('usage-logs.index') }}"
                        class="text-xs text-blue-600 hover:underline font-semibold">
                        すべての履歴を見る →
                    </a>
                </div>
            </div>

        </div>
    </div>

</x-layouts.app>
