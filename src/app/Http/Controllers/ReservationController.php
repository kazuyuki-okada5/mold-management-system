<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Mold;
use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $service) {}

    // 予約一覧
    // admin → 全件、operator → 自分の予約のみ
    public function index(Request $request)
    {
        $user = auth()->user();

        $reservations = Reservation::with(['mold', 'user'])
            ->when($user->role !== 'admin',
                fn($q) => $q->where('user_id', $user->id))
            ->when($request->status,
                fn($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('reservations.index', compact('reservations'));
    }

    // 予約申請フォーム表示(URLクエリに mold_id を受け取ると金型を初期選択した状態で表示する)
    public function create(Request $request)
    {
        // 金型詳細ページの「予約申請」ボタンから遷移した場合
        $mold = $request->mold_id
            ? Mold::findOrFail($request->mold_id)
            : null;

        // 待機中の金型一覧（フォームのselect用）
        $molds = Mold::whereIn('status', ['待機中', '予約済み'])
            ->orderBy('mold_number')
            ->get();

        return view('reservations.create', compact('mold', 'molds'));
    }

    // 予約申請を保存
    public function store(StoreReservationRequest $request)
    {
        $start = Carbon::parse($request->reserved_start);
        $end   = Carbon::parse($request->reserved_end);

        // 重複チェック
        if ($this->service->checkOverlap($request->mold_id, $start, $end)) {
            return back()
                ->withErrors(['reserved_start' => 'この時間帯は既に予約が入っています。別の時間帯を選択してください。'])
                ->withInput();
        }

        Reservation::create([
            'mold_id'        => $request->mold_id,
            'user_id'        => auth()->id(),
            'reserved_start' => $start,
            'reserved_end'   => $end,
            'purpose'        => $request->purpose,
            'status'         => 'pending',
        ]);

        return redirect()->route('reservations.index')
            ->with('success', '予約申請を送信しました。管理者の承認をお待ちください。');
    }

    // 予約詳細
    public function show(Reservation $reservation)
    {
        // operator は自分の予約のみ閲覧可
        $user = auth()->user();
        if ($user->role !== 'admin' && $reservation->user_id !== $user->id) {
            abort(403);
        }

        $reservation->load(['mold', 'user', 'approver']);

        return view('reservations.show', compact('reservation'));
    }

    // 予約キャンセル（申請者本人のみ・pending か approved のみ）
    public function cancel(Reservation $reservation)
    {
        $user = auth()->user();

        // 権限チェック：本人 or admin
        if ($user->role !== 'admin' && $reservation->user_id !== $user->id) {
            abort(403);
        }

        // キャンセル可能なステータスか確認
        if (! in_array($reservation->status, ['pending', 'approved'])) {
            return back()->with('error', 'この予約はキャンセルできません。');
        }

        $this->service->cancel($reservation);

        return back()->with('success', '予約をキャンセルしました。');
    }
}
