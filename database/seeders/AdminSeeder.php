<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@fanstore.com'],
            [
                'name' => 'FanStore Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'balance' => 0.00,
                'is_active' => true,
            ]
        );
    }
}
