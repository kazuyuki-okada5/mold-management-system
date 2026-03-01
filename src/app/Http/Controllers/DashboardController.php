<?php

namespace App\Http\Controllers;

use App\Models\Mold;
use App\Models\Reservation;
use App\Models\UsageLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // 金型サマリー
        $moldStats = (object) [
            'total'   => Mold::count(),
            'standby' => Mold::where('status', '待機中')->count(),
            'in_use'  => Mold::where('status', '使用中')->count(),
        ];

        // 自分の予約件数（pending + approved）
        $myReservationCount = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('reserved_end', '>=', now())
            ->count();

        // 自分の直近の予約（3件）
        $myReservations = Reservation::with('mold')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('reserved_end', '>=', now())
            ->orderBy('reserved_start')
            ->limit(3)
            ->get();

        // 自分の最近の使用履歴（3件）
        $myLogs = UsageLog::with('mold')
            ->where('user_id', $user->id)
            ->whereNotNull('end_time')
            ->latest('start_time')
            ->limit(3)
            ->get();

        return view('dashboard', compact(
            'moldStats',
            'myReservationCount',
            'myReservations',
            'myLogs',
        ));
    }
}
