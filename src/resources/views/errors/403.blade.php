<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 アクセス拒否 | 金型管理システム</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-sm antialiased">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <div class="text-6xl mb-4">🔒</div>
            <p class="text-8xl font-black text-slate-200 leading-none">403</p>
            <h1 class="text-2xl font-bold text-slate-700 mt-2">アクセス拒否</h1>
            <p class="text-slate-500 mt-2 mb-6">このページへのアクセス権限がありません。</p>
            <a href="{{ url('/dashboard') }}"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                ダッシュボードへ戻る
            </a>
        </div>
    </div>
</body>
</html>