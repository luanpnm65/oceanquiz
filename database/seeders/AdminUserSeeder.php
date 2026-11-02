<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Tài khoản Admin mặc định
        User::updateOrCreate(
            ['email' => 'admin@oceanquiz.test'],
            [
                'name' => 'Quản trị viên OceanQuiz',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_locked' => false,
            ]
        );

        // Tài khoản Người dùng mẫu để test nhanh
        User::updateOrCreate(
            ['email' => 'user@oceanquiz.test'],
            [
                'name' => 'Thợ Lặn Demo',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_locked' => false,
            ]
        );
    }
}