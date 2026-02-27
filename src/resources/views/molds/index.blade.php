<x-layouts.app title="金型管理" subtitle="金型の一覧・検索・操作">

    <div class="space-y-4">

        {{-- 寿命アラートバナー --}}
        @if ($alerts->isNotEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-3">
                <span class="text-amber-500 text-lg flex-shrink-0 mt-0.5">⚠️</span>
                <div>
                    <p class="text-sm text-amber-800 font-semibold mb-1">寿命80%超え — 早めのメンテナンスをご確認ください</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($alerts as $alert)
                            <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-mono font-bold">
                                {{ $alert->mold_number }} {{ $alert->name }}
                                ({{ $alert->usage_rate }}%)
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- 検索フィルター --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <form method="GET" action="{{ route('molds.index') }}">
                <div class="grid grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">金型番号</label>
                        <input type="text" name="mold_number" value="{{ request('mold_number') }}"
                            placeholder="M-001"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">状態</label>
                        <select name="status"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">すべて</option>
                            @foreach (['待機中', '使用中', '予約済み', 'メンテナンス中'] as $s)
                                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">倉庫</label>
                        <select name="warehouse"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">すべて</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w }}" @selected(request('warehouse') === $w)>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">フロア</label>
                        <select name="floor"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">すべて</option>
                            @foreach ($floors as $f)
                                <option value="{{ $f }}" @selected(request('floor') === $f)>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                            検索
                        </button>
                        <a href="{{ route('molds.index') }}"
                            class="px-4 py-2 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold transition-colors">
                            リセット
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- テーブル --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-700">
                    {{ $molds->total() }}件中 {{ $molds->firstItem() }}〜{{ $molds->lastItem() }}件表示
                </p>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.molds.create') }}"
                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-colors">
                        ＋ 新規登録
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            @foreach (['金型番号', '名称', '状態', '保管場所', '累計使用回数', '操作'] as $h)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">
                                    {{ $h }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($molds as $mold)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-blue-700">
                                    {{ $mold->mold_number }}
                                </td>
                                <td class="px-4 py-3 text-slate-800 font-medium">
                                    {{ $mold->name }}
                                    @if ($mold->nearing_limit)
                                        <span class="ml-1 text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-semibold">⚠️ 要点検</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$mold->status" />
                                </td>
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                    {{ implode(' ', array_filter([$mold->warehouse, $mold->floor, $mold->shelf_number])) ?: '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($mold->max_usage_count)
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-slate-200 rounded-full h-1.5 w-20">
                                                <div class="h-1.5 rounded-full {{ $mold->usage_rate >= 80 ? 'bg-red-500' : 'bg-blue-500' }}"
                                                    style="width: {{ min($mold->usage_rate, 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-slate-500 whitespace-nowrap">
                                                {{ $mold->total_usage_count }}/{{ $mold->max_usage_count }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-slate-500">{{ $mold->total_usage_count }}回</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1.5 flex-wrap">
                                        <a href="{{ route('molds.show', $mold) }}"
                                            class="px-3 py-1.5 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
                                            詳細
                                        </a>
                                        @if ($mold->status === '待機中')
                                            <form method="POST" action="{{ route('molds.use-start', $mold) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-colors">
                                                    使用開始
                                                </button>
                                            </form>
                                            <a href="{{ route('reservations.create', ['mold_id' => $mold->id]) }}"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
                                                予約
                                            </a>
                                        @endif
                                        @if ($mold->status === '使用中' && $mold->activeUsage?->user_id === auth()->id())
                                            <form method="POST" action="{{ route('molds.use-end', $mold) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition-colors"
                                                    onclick="return confirm('使用を終了しますか？')">
                                                    使用終了
                                                </button>
                                            </form>
                                        @endif
                                        @if (auth()->user()->role === 'admin')
                                            <a href="{{ route('admin.molds.edit', $mold) }}"
                                                class="px-3 py-1.5 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
                                                編集
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                                    金型が見つかりませんでした
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ページネーション --}}
            @if ($molds->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $molds->links() }}
                </div>
            @endif
        </div>

    </div>

</x-layouts.app>