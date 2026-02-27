<?php

namespace Database\Seeders;

use App\Models\Reservation;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        // 承認済み（未来）
        Reservation::create([
            'mold_id'        => 1,
            'user_id'        => 2,
            'reserved_start' => now()->addDays(3)->setTime(9, 0),
            'reserved_end'   => now()->addDays(3)->setTime(12, 0),
            'status'         => 'approved',
            'purpose'        => '量産試作品の成形テスト',
            'approved_by'    => 1,
            'approved_at'    => now(),
        ]);

        // 承認待ち（未来）
        Reservation::create([
            'mold_id'        => 2,
            'user_id'        => 3,
            'reserved_start' => now()->addDays(5)->setTime(13, 0),
            'reserved_end'   => now()->addDays(5)->setTime(17, 0),
            'status'         => 'pending',
            'purpose'        => '新製品の試作',
        ]);

        // 完了（過去）
        Reservation::create([
            'mold_id'        => 1,
            'user_id'        => 2,
            'reserved_start' => now()->subDays(7)->setTime(9, 0),
            'reserved_end'   => now()->subDays(7)->setTime(12, 0),
            'status'         => 'completed',
            'purpose'        => '定期検査用サンプル成形',
            'approved_by'    => 1,
            'approved_at'    => now()->subDays(8),
        ]);
    }
}
