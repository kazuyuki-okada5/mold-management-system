<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $service) {}

    // 承認
    public function approve(Reservation $reservation)
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'この予約は承認できる状態ではありません。');
        }

        $this->service->approve($reservation);

        return back()->with('success', '予約を承認しました。');
    }

    // 否認
    public function reject(Request $request, Reservation $reservation)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ], [
            'reject_reason.required' => '否認理由を入力してください。',
        ]);

        if ($reservation->status !== 'pending') {
            return back()->with('error', 'この予約は否認できる状態ではありません。');
        }

        $this->service->reject($reservation, $request->reject_reason);

        return back()->with('success', '予約を否認しました。');
    }
}
