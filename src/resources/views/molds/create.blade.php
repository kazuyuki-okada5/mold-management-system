<x-layouts.app title="金型登録" subtitle="新しい金型を登録します">

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">

            <form method="POST" action="{{ route('admin.molds.store') }}">
                @csrf

                {{-- バリデーションエラー一覧 --}}
                @if ($errors->any())
                    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                        <p class="text-sm font-semibold text-red-700 mb-1">入力内容を確認してください</p>
                        <ul class="list-disc list-inside text-xs text-red-600 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-5">

                    {{-- 金型番号・名称 --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                金型番号 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="mold_number" value="{{ old('mold_number') }}"
                                placeholder="M-043"
                                class="w-full border @error('mold_number') border-red-400 @else border-slate-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('mold_number')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                金型名 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="射出成形金型F"
                                class="w-full border @error('name') border-red-400 @else border-slate-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 仕様・メモ --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">仕様・メモ</label>
                        <textarea name="specifications" rows="3" placeholder="材質、耐熱温度、特記事項など..."
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('specifications') }}</textarea>
                    </div>

                    {{-- 製造日・最大使用回数 --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                製造日 <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="manufacture_date" value="{{ old('manufacture_date') }}"
                                class="w-full border @error('manufacture_date') border-red-400 @else border-slate-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('manufacture_date')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">最大使用回数（寿命）</label>
                            <input type="number" name="max_usage_count" value="{{ old('max_usage_count') }}"
                                placeholder="200" min="1"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- 保管場所 --}}
                    <div class="border-t border-slate-100 pt-5">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">保管場所</p>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">倉庫</label>
                                <select name="warehouse"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">選択してください</option>
                                    @foreach (['A棟', 'B棟', 'C棟'] as $w)
                                        <option value="{{ $w }}" @selected(old('warehouse') === $w)>{{ $w }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">フロア</label>
                                <select name="floor"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">選択してください</option>
                                    @foreach (['1F', '2F', '3F'] as $f)
                                        <option value="{{ $f }}" @selected(old('floor') === $f)>{{ $f }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">棚番号</label>
                                <input type="text" name="shelf_number" value="{{ old('shelf_number') }}"
                                    placeholder="A-01"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- ボタン --}}
                    <div class="flex gap-3 justify-end pt-2 border-t border-slate-100">
                        <a href="{{ route('molds.index') }}"
                            class="px-4 py-2 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold transition-colors">
                            キャンセル
                        </a>
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                            登録する
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

</x-layouts.app>