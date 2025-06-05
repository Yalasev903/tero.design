<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@tero.design'],
            [
                'name' => 'Админ',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
