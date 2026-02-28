{{-- resources/views/components/reservation-status-badge.blade.php --}}
@props(['status'])

@php
$map = [
    'pending'   => ['bg' => 'bg-amber-100',   'text' => 'text-amber-700',   'dot' => 'bg-amber-500',   'label' => '承認待ち'],
    'approved'  => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => '承認済み'],
    'rejected'  => ['bg' => 'bg-red-100',     'text' => 'text-red-700',     'dot' => 'bg-red-500',     'label' => '否認'],
    'cancelled' => ['bg' => 'bg-slate-100',   'text' => 'text-slate-500',   'dot' => 'bg-slate-400',   'label' => 'キャンセル'],
    'completed' => ['bg' => 'bg-blue-100',    'text' => 'text-blue-700',    'dot' => 'bg-blue-500',    'label' => '完了'],
];
$s = $map[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400', 'label' => $status];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $s['bg'] }} {{ $s['text'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
    {{ $s['label'] }}
</span>