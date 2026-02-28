<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['user_id' => 'admin'],
            [
                'user_name' => '관리자',
                'email' => 'admin@yourmemorial.kr',
                'user_password' => Hash::make('admin1234!'),
                'is_admin' => 1,
            ]
        );
    }
}
