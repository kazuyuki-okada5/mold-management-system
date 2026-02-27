@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';

    $adminNav = [
        ['route' => 'admin.dashboard', 'label' => '管理ダッシュボード', 'icon' => '📊'],
        ['route' => 'molds.index',     'label' => '金型管理', 'icon' => '🔩'],
        ['route' => 'reservations.index', 'label' => '予約管理', 'icon' => '📅'],
        ['route' => 'usage-logs.index','label' => '使用履歴', 'icon' => '📋'],
        ['route' => 'admin.users.index','label' => 'ユーザー管理', 'icon' => '👥'],
        ['route' => 'admin.molds.stats','label' => '稼働率', 'icon' => '📈'],
    ];

    $userNav = [
        ['route' => 'dashboard',          'label' => 'ダッシュボード', 'icon' => '🏠'],
        ['route' => 'molds.index',        'label' => '金型一覧', 'icon' => '🔩'],
        ['route' => 'reservations.index', 'label' => '予約管理', 'icon' => '📅'],
        ['route' => 'usage-logs.index',   'label' => '使用履歴', 'icon' => '📋'],
    ];

    $nav = $isAdmin ? $adminNav : $userNav;
@endphp

<aside class="w-56 bg-slate-900 text-white flex flex-col flex-shrink-0">
    {{-- ロゴ --}}
    <div class="px-5 py-5 border-b border-slate-700">
        <div class="flex items-center gap-2">
            <span class="text-2xl">⚙️</span>
            <div>
                <p class="text-xs text-slate-400 leading-none">金型管理</p>
                <p class="text-sm font-bold leading-tight">MOLD SYSTEM</p>
            </div>
        </div>
    </div>

    {{-- ナビゲーション --}}
    <nav class="flex-1 py-4 px-2 space-y-0.5 overflow-y-auto">
        @foreach ($nav as $item)
            @php
                try {
                    $isActive = request()->routeIs($item['route']);
                } catch (\Exception $e) {
                    $isActive = false;
                }
            @endphp
            <a href="{{ route($item['route']) }}"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                    {{ $isActive
                        ? 'bg-blue-600 text-white font-semibold'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- ユーザー情報 --}}
    <div class="px-4 py-4 border-t border-slate-700 flex-shrink-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>
</aside>