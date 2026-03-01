<x-layouts.app title="ユーザー管理" subtitle="システム利用者の管理">

    <div class="space-y-4">

        {{-- テーブル --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-700">
                    登録ユーザー: {{ $users->count() }}名
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            @foreach(['氏名', 'メールアドレス', 'ロール', '部署', '登録日'] as $h)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    {{ $h }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 transition-colors">

                                {{-- 氏名 --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-200 flex items-center justify-center text-xs font-bold text-blue-700 flex-shrink-0">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ $user->name }}</span>
                                    </div>
                                </td>

                                {{-- メールアドレス --}}
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $user->email }}
                                </td>

                                {{-- ロール --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold
                                        {{ $user->role === 'admin'
                                            ? 'bg-purple-100 text-purple-700'
                                            : 'bg-slate-100 text-slate-600' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>

                                {{-- 部署 --}}
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $user->department ?? '—' }}
                                </td>

                                {{-- 登録日 --}}
                                <td class="px-4 py-3 text-slate-500 text-xs">
                                    {{ $user->created_at->format('Y/m/d') }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-slate-400 text-sm">
                                    ユーザーが見つかりませんでした
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-layouts.app>