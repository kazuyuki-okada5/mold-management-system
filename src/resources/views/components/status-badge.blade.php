@props(['status', 'type' => 'mold'])

@php
    if ($type === 'mold') {
      // 金型ステータス用のカラーマップ
        $map = [
            '待機中' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
            '使用中' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
            '予約済み' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500'],
            'メンテナンス中' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'],
        ];
        // 金型ステータスはそのままラベルに使う
        $label = $status;
    } else {
      // 予約ステータス用のカラーマップ
        $map = [
            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500'],
            'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
            'cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'dot' => 'bg-slate-400'],
            'completed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
        ];
        // 予約ステータスは英語のままなので日本語に変換する
        $labelMap = [
            'pending' => '承認待ち',
            'approved' => '承認済み',
            'rejected' => '否認',
            'cancelled' => 'キャンセル',
            'completed' => '完了',
        ];
        // $labelMapに存在しない値はそのまま表示
        $label = $labelMap[$status] ?? $status;
    }

    // マップに存在しないステータスはグレーで表示
    $s = $map[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'];
@endphp

{{-- ステータスバッジ本体：丸いドット＋ラベルテキスト --}}
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $s['bg'] }} {{ $s['text'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
    {{ $label }}
</span>