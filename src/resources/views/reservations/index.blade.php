{{-- resources/views/reservations/index.blade.php --}}
<x-layouts.app title="予約管理" subtitle="金型の予約申請・承認管理">
    <div class="space-y-4">

        {{-- ページヘッダー --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">予約管理</h2>
                <p class="text-xs text-slate-500 mt-0.5">金型の予約申請・承認管理</p>
            </div>
            <a href="{{ route('reservations.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                ＋ 予約申請
            </a>
        </div>

        {{-- ステータスフィルター --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach(['' => 'すべて', 'pending' => '承認待ち', 'approved' => '承認済み', 'rejected' => '否認', 'cancelled' => 'キャンセル', 'completed' => '完了'] as $val => $label)
                <a href="{{ route('reservations.index', $val ? ['status' => $val] : []) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors
                       {{ request('status') === $val || (request('status') === null && $val === '')
                           ? 'bg-blue-600 text-white'
                           : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- テーブル --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            @foreach(['金型', '予約者', '開始日時', '終了日時', '使用目的', '状態', '操作'] as $h)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($reservations as $r)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-mono font-bold text-blue-700 text-xs">{{ $r->mold->mold_number }}</p>
                                    <p class="text-slate-700 text-xs">{{ $r->mold->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $r->user->name }}</td>
                                <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($r->reserved_start)->format('Y/m/d H:i') }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($r->reserved_end)->format('Y/m/d H:i') }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs max-w-xs truncate">
                                    {{ $r->purpose }}
                                </td>
                                <td class="px-4 py-3">
                                    <x-reservation-status-badge :status="$r->status" />
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1.5 flex-wrap">
                                        <a href="{{ route('reservations.show', $r) }}"
                                           class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                                            詳細
                                        </a>

                                        {{-- 管理者：承認・否認 --}}
                                        @if(auth()->user()->role === 'admin' && $r->status === 'pending')
                                            <form action="{{ route('admin.reservations.approve', $r) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                                                    承認
                                                </button>
                                            </form>
                                        @endif

                                        {{-- キャンセルボタン（本人 or admin、pending/approved のみ） --}}
                                        @if(in_array($r->status, ['pending', 'approved']) &&
                                            (auth()->user()->role === 'admin' || $r->user_id === auth()->id()))
                                            <form action="{{ route('reservations.cancel', $r) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('キャンセルしてよろしいですか？')">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                                    キャンセル
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400 text-sm">
                                    予約データがありません
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ページネーション --}}
            @if($reservations->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $reservations->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>