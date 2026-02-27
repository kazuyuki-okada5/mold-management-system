<?php

namespace Database\Seeders;

use App\Models\Mold;
use Illuminate\Database\Seeder;

class MoldSeeder extends Seeder
{
    public function run(): void
    {
        $molds = [
            [
                'mold_number'       => 'M-001',
                'name'              => '射出成形金型A',
                'specifications'    => 'アルミ合金製、最大500℃耐熱',
                'manufacture_date'  => '2020-04-01',
                'status'            => '待機中',
                'warehouse'         => 'A棟',
                'floor'             => '1F',
                'shelf_number'      => 'A-01',
                'total_usage_count' => 145,
                'max_usage_count'   => 200,
            ],
            [
                'mold_number'       => 'M-002',
                'name'              => 'ダイカスト金型B',
                'specifications'    => '鉄鋼製、最大800℃耐熱',
                'manufacture_date'  => '2019-06-15',
                'status'            => '待機中',
                'warehouse'         => 'A棟',
                'floor'             => '2F',
                'shelf_number'      => 'B-03',
                'total_usage_count' => 88,
                'max_usage_count'   => 150,
            ],
            [
                'mold_number'       => 'M-003',
                'name'              => 'プレス金型C',
                'specifications'    => 'ステンレス製',
                'manufacture_date'  => '2018-01-10',
                'status'            => '待機中',
                'warehouse'         => 'B棟',
                'floor'             => '1F',
                'shelf_number'      => 'C-07',
                'total_usage_count' => 210,
                'max_usage_count'   => 200,
            ],
            [
                'mold_number'       => 'M-004',
                'name'              => '射出成形金型D',
                'specifications'    => 'アルミ合金製',
                'manufacture_date'  => '2021-09-01',
                'status'            => 'メンテナンス中',
                'warehouse'         => 'C棟',
                'floor'             => '3F',
                'shelf_number'      => 'D-02',
                'total_usage_count' => 55,
                'max_usage_count'   => 200,
            ],
            [
                'mold_number'       => 'M-005',
                'name'              => 'プレス金型E',
                'specifications'    => '鉄鋼製',
                'manufacture_date'  => '2022-03-20',
                'status'            => '待機中',
                'warehouse'         => 'A棟',
                'floor'             => '1F',
                'shelf_number'      => 'A-05',
                'total_usage_count' => 30,
                'max_usage_count'   => 200,
            ],
        ];

        foreach ($molds as $mold) {
            Mold::create($mold);
        }
    }
}