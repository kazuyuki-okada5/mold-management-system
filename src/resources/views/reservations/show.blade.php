{{-- resources/views/reservations/show.blade.php --}}
<x-layouts.app title="予約詳細" subtitle="予約ID: #{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}">
    <div class="max-w-lg space-y-4">

        {{-- 予約情報 --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-700">予約情報</h3>
                <x-reservation-status-badge :status="$reservation->status" />
            </div>

            <dl class="space-y-0 text-sm divide-y divide-slate-100">
                <div class="flex justify-between py-2.5">
                    <dt class="text-slate-500">金型</dt>
                    <dd class="font-semibold text-slate-800 text-right">
                        <span class="font-mono text-blue-700">{{ $reservation->mold->mold_number }}</span>
                        / {{ $reservation->mold->name }}
                    </dd>
                </div>
                <div class="flex justify-between py-2.5">
                    <dt class="text-slate-500">予約者</dt>
                    <dd class="font-semibold text-slate-800">
                        {{ $reservation->user->name }}
                        @if($reservation->user->department)
                            <span class="text-xs text-slate-500 font-normal">（{{ $reservation->user->department }}）</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between py-2.5">
                    <dt class="text-slate-500">予約開始</dt>
                    <dd class="font-semibold text-slate-800">
                        {{ \Carbon\Carbon::parse($reservation->reserved_start)->format('Y/m/d H:i') }}
                    </dd>
                </div>
                <div class="flex justify-between py-2.5">
                    <dt class="text-slate-500">予約終了</dt>
                    <dd class="font-semibold text-slate-800">
                        {{ \Carbon\Carbon::parse($reservation->reserved_end)->format('Y/m/d H:i') }}
                    </dd>
                </div>
                <div class="flex justify-between py-2.5">
                    <dt class="text-slate-500">使用目的</dt>
                    <dd class="font-semibold text-slate-800 text-right max-w-xs">{{ $reservation->purpose }}</dd>
                </div>
                <div class="flex justify-between py-2.5">
                    <dt class="text-slate-500">申請日時</dt>
                    <dd class="text-slate-600">{{ $reservation->created_at->format('Y/m/d H:i') }}</dd>
                </div>
                @if($reservation->approver)
                    <div class="flex justify-between py-2.5">
                        <dt class="text-slate-500">承認者</dt>
                        <dd class="font-semibold text-slate-800">{{ $reservation->approver->name }}</dd>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <dt class="text-slate-500">承認日時</dt>
                        <dd class="text-slate-600">{{ \Carbon\Carbon::parse($reservation->approved_at)->format('Y/m/d H:i') }}</dd>
                    </div>
                @endif
                @if($reservation->reject_reason)
                    <div class="py-2.5">
                        <dt class="text-slate-500 mb-1">否認理由</dt>
                        <dd class="text-red-700 font-medium bg-red-50 rounded-lg px-3 py-2 text-sm">
                            {{ $reservation->reject_reason }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- 管理者用承認パネル（pending のみ表示） --}}
        @if(auth()->user()->role === 'admin' && $reservation->status === 'pending')
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-5">
                <h3 class="font-bold text-blue-800 mb-4 text-sm">📋 承認操作（管理者）</h3>

                {{-- 承認フォーム --}}
                <form action="{{ route('admin.reservations.approve', $reservation) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit"
                        onclick="return confirm('この予約を承認しますか？')"
                        class="w-full py-2.5 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                        ✅ 承認する
                    </button>
                </form>

                {{-- 否認フォーム --}}
                <form action="{{ route('admin.reservations.reject', $reservation) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">否認理由 <span class="text-red-500">*</span></label>
                            <textarea name="reject_reason" rows="3"
                                placeholder="理由を入力してください..."
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none
                                       @error('reject_reason') border-red-400 @enderror">{{ old('reject_reason') }}</textarea>
                            @error('reject_reason')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                            onclick="return confirm('この予約を否認しますか？')"
                            class="w-full py-2.5 text-sm font-semibold rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">
                            ❌ 否認する
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- キャンセルボタン --}}
        @if(in_array($reservation->status, ['pending', 'approved']) &&
            (auth()->user()->role === 'admin' || $reservation->user_id === auth()->id()))
            <form action="{{ route('reservations.cancel', $reservation) }}" method="POST"
                  onsubmit="return confirm('この予約をキャンセルしますか？')">
                @csrf
                <button type="submit"
                    class="w-full py-2.5 text-sm font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                    予約をキャンセルする
                </button>
            </form>
        @endif

        {{-- 戻るボタン --}}
        <a href="{{ route('reservations.index') }}"
           class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-800 transition-colors">
            ← 一覧に戻る
        </a>

    </div>
</x-layouts.app>