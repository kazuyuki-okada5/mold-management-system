<x-layouts.app :title="$mold->mold_number . ' / ' . $mold->name" subtitle="金型詳細">

    <div class="space-y-5">

        {{-- ヘッダーカード --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center text-2xl flex-shrink-0">🔩</div>
                    <div>
                        <div class="flex items-center gap-3 mb-1 flex-wrap">
                            <span class="font-mono text-lg font-bold text-blue-700">{{ $mold->mold_number }}</span>
                            <x-status-badge :status="$mold->status" />
                            @if ($mold->nearing_limit)
                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-full font-semibold">
                                    ⚠️ 寿命{{ $mold->usage_rate }}%
                                </span>
                            @endif
                        </div>
                        <p class="text-xl font-bold text-slate-800">{{ $mold->name }}</p>
                        <p class="text-sm text-slate-500 mt-0.5">
                            {{ implode(' ｜ ', array_filter([$mold->warehouse . $mold->floor, $mold->shelf_number])) }}
                            @if ($mold->manufacture_date)
                                ｜ 製造日: {{ $mold->manufacture_date->format('Y/m/d') }}
                            @endif
                        </p>
                        @if ($mold->activeUsage)
                            <p class="text-xs text-red-600 font-semibold mt-1">
                                🔴 使用中: {{ $mold->activeUsage->user->name }}（{{ $mold->activeUsage->start_time->format('H:i') }}〜）
                            </p>
                        @endif
                    </div>
                </div>

                {{-- 操作ボタン --}}
                <div class="flex gap-2 flex-wrap flex-shrink-0">
                    @if ($mold->status === '待機中')
                        <form method="POST" action="{{ route('molds.use-start', $mold) }}">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition-colors">
                                使用開始
                            </button>
                        </form>
                        <a href="{{ route('reservations.create', ['mold_id' => $mold->id]) }}"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition-colors">
                            予約申請
                        </a>
                    @endif
                    @if ($mold->status === '使用中' && $mold->activeUsage?->user_id === auth()->id())
                        <form method="POST" action="{{ route('molds.use-end', $mold) }}">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors"
                                onclick="return confirm('使用を終了しますか？')">
                                使用終了
                            </button>
                        </form>
                    @endif
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.molds.edit', $mold) }}"
                            class="px-3 py-2 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold transition-colors">
                            編集
                        </a>
                        <form method="POST" action="{{ route('admin.molds.destroy', $mold) }}"
                            onsubmit="return confirm('この金型を削除しますか？この操作は取り消せません。')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors">
                                削除
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- 基本情報 ＋ 予約状況 --}}
        <div class="grid grid-cols-3 gap-5">

            {{-- 基本情報 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <h2 class="font-bold text-slate-700 mb-4 text-sm">基本情報</h2>
                <dl class="text-sm">
                    @foreach ([
                        ['仕様', $mold->specifications ?: '—'],
                        ['倉庫', $mold->warehouse ?: '—'],
                        ['フロア', $mold->floor ?: '—'],
                        ['棚番号', $mold->shelf_number ?: '—'],
                        ['製造日', $mold->manufacture_date?->format('Y/m/d') ?: '—'],
                        ['累計使用回数', $mold->total_usage_count . '回' . ($mold->max_usage_count ? ' / 最大' . $mold->max_usage_count . '回' : '')],
                        ['今月使用回数', $stats->this_month_count . '回'],
                        ['総使用時間', intdiv($stats->total_minutes, 60) . 'h' . ($stats->total_minutes % 60) . 'm'],
                    ] as [$key, $val])
                        <div class="flex justify-between py-2 border-b border-slate-100 last:border-0">
                            <dt class="text-slate-500">{{ $key }}</dt>
                            <dd class="font-semibold text-slate-800 text-right max-w-[60%] break-all">{{ $val }}</dd>
                        </div>
                    @endforeach
                </dl>

                {{-- 寿命プログレスバー --}}
                @if ($mold->max_usage_count)
                    <div class="mt-4">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-500">寿命使用率</span>
                            <span class="font-bold {{ $mold->usage_rate >= 80 ? 'text-red-600' : 'text-blue-600' }}">
                                {{ $mold->usage_rate }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $mold->usage_rate >= 80 ? 'bg-red-500' : 'bg-blue-500' }}"
                                style="width: {{ min($mold->usage_rate, 100) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 今後の予約状況 --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 col-span-2">
                <h2 class="font-bold text-slate-700 mb-4 text-sm">今後の予約状況</h2>
                @if ($mold->reservations->isEmpty())
                    <p class="text-slate-400 text-sm py-4 text-center">予約はありません</p>
                @else
                    <div class="space-y-2">
                        @foreach ($mold->reservations as $reservation)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg text-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-full bg-blue-200 flex items-center justify-center text-xs font-bold text-blue-700 flex-shrink-0">
                                        {{ mb_substr($reservation->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $reservation->user->name }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $reservation->reserved_start->format('Y/m/d H:i') }}
                                            〜
                                            {{ $reservation->reserved_end->format('H:i') }}
                                        </p>
                                        @if ($reservation->purpose)
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $reservation->purpose }}</p>
                                        @endif
                                    </div>
                                </div>
                                <x-status-badge :status="$reservation->status" type="reservation" />
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- 使用履歴（直近10件） --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="font-bold text-slate-700 mb-4 text-sm">使用履歴（直近10件）</h2>
            @if ($mold->usageLogs->isEmpty())
                <p class="text-slate-400 text-sm py-4 text-center">使用履歴はありません</p>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 rounded-lg">
                        <tr>
                            @foreach (['使用者', '開始日時', '終了日時', '使用時間'] as $h)
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($mold->usageLogs as $log)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-700">
                                    {{ $log->user->name ?? '（削除済み）' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs">
                                    {{ $log->start_time->format('Y/m/d H:i') }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($log->end_time)
                                        <span class="text-slate-600">{{ $log->end_time->format('Y/m/d H:i') }}</span>
                                    @else
                                        <span class="text-red-600 font-semibold">使用中</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($log->duration_minutes !== null)
                                        <span class="font-bold text-blue-700">{{ $log->duration_minutes }}分</span>
                                        <span class="text-xs text-slate-400 ml-1">
                                            ({{ intdiv($log->duration_minutes, 60) }}h{{ $log->duration_minutes % 60 }}m)
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- 全件リンク --}}
                @if ($stats->total_count > 10)
                    <div class="mt-3 pt-3 border-t border-slate-100 text-right">
                        <a href="{{ route('usage-logs.index', ['mold_number' => $mold->mold_number]) }}"
                            class="text-xs text-blue-600 hover:underline">
                            全{{ $stats->total_count }}件の履歴を見る →
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-layouts.app>