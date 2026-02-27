<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '金型管理システム' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-sm antialiased">

<div class="flex h-screen overflow-hidden">
    {{-- サイドバー --}}
    @include('components.layouts.sidebar')

    {{-- メインコンテンツ --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- ヘッダー --}}
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-lg font-bold text-slate-800">{{ $title ?? '' }}</h1>
                @isset($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-xs text-slate-500 hover:text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                        ログアウト
                    </button>
                </form>
            </div>
        </header>

        {{-- フラッシュメッセージ --}}
        @if (session('success') || session('error'))
            <div class="px-6 pt-4">
                @if (session('success'))
                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl">
                        <span>✅</span> {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl">
                        <span>❌</span> {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- メインエリア --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>

        {{-- フッター --}}
        <footer class="bg-white border-t border-slate-200 px-6 py-3 text-xs text-slate-400 flex justify-between flex-shrink-0">
            <span>金型ライフサイクル管理システム v1.0.0</span>
            <span>© 2026 Mold Management</span>
        </footer>
    </div>
</div>

</body>
</html>