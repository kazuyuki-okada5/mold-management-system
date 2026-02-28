{{-- resources/views/reservations/create.blade.php --}}
<x-layouts.app title="予約申請" subtitle="金型の使用予約を申請します">
    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <form action="{{ route('reservations.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    {{-- 金型選択 --}}
                    @if($mold)
                        <input type="hidden" name="mold_id" value="{{ $mold->id }}">
                        <div class="p-4 bg-blue-50 rounded-xl flex items-center gap-3">
                            <span class="text-2xl">🔩</span>
                            <div>
                                <p class="font-mono font-bold text-blue-800 text-sm">{{ $mold->mold_number }}</p>
                                <p class="font-semibold text-slate-800">{{ $mold->name }}</p>
                                <x-mold-status-badge :status="$mold->status" />
                            </div>
                        </div>
                    @else
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">金型を選択 <span class="text-red-500">*</span></label>
                            <select name="mold_id"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white
                                       @error('mold_id') border-red-400 @enderror">
                                <option value="">-- 選択してください --</option>
                                @foreach($molds as $m)
                                    <option value="{{ $m->id }}" {{ old('mold_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->mold_number }} / {{ $m->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mold_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- 日時 --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">予約開始日時 <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="reserved_start"
                                value="{{ old('reserved_start') }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                       @error('reserved_start') border-red-400 @enderror">
                            @error('reserved_start')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">予約終了日時 <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="reserved_end"
                                value="{{ old('reserved_end') }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                       @error('reserved_end') border-red-400 @enderror">
                            @error('reserved_end')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 使用目的 --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">使用目的 <span class="text-red-500">*</span></label>
                        <textarea name="purpose" rows="3"
                            placeholder="量産試作品の成形テスト..."
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none
                                   @error('purpose') border-red-400 @enderror">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 注意文 --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800">
                        ⚠️ 予約は管理者の承認後に確定します。承認前は他の予約と重複する場合があります。
                    </div>

                    {{-- ボタン --}}
                    <div class="flex gap-3 justify-end pt-2 border-t border-slate-100">
                        <a href="{{ route('reservations.index') }}"
                           class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            キャンセル
                        </a>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                            申請する
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-layouts.app>