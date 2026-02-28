{{-- resources/views/components/mold-status-badge.blade.php --}}
@props(['status'])

@php
$map = [
    '待機中'        => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
    '使用中'        => ['bg' => 'bg-red-100',     'text' => 'text-red-700',     'dot' => 'bg-red-500'],
    '予約済み'      => ['bg' => 'bg-amber-100',   'text' => 'text-amber-700',   'dot' => 'bg-amber-500'],
    'メンテナンス中' => ['bg' => 'bg-slate-100',  'text' => 'text-slate-600',   'dot' => 'bg-slate-400'],
];
$s = $map[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $s['bg'] }} {{ $s['text'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
    {{ $status }}
</span>