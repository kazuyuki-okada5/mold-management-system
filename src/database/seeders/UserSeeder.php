<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'       => '管理者',
            'email'      => 'admin@example.com',
            'password'   => bcrypt('password'),
            'role'       => 'admin',
            'department' => '管理部',
        ]);

        User::create([
            'name'       => '田中 一郎',
            'email'      => 'tanaka@example.com',
            'password'   => bcrypt('password'),
            'role'       => 'operator',
            'department' => '製造部A',
        ]);

        User::create([
            'name'       => '佐藤 花子',
            'email'      => 'sato@example.com',
            'password'   => bcrypt('password'),
            'role'       => 'operator',
            'department' => '製造部B',
        ]);
    }
}
