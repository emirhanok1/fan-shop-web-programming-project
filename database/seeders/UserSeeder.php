<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ahmet Yılmaz',
                'email' => 'user1@fanstore.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'balance' => 250.00,
                'is_active' => true,
            ],
            [
                'name' => 'Elif Kaya',
                'email' => 'user2@fanstore.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'balance' => 250.00,
                'is_active' => true,
            ],
            [
                'name' => 'Mehmet Demir',
                'email' => 'user3@fanstore.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'balance' => 250.00,
                'is_active' => true,
            ],
            [
                'name' => 'Ayşe Öztürk',
                'email' => 'user4@fanstore.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'balance' => 250.00,
                'is_active' => true,
            ],
            [
                'name' => 'Can Yıldız',
                'email' => 'user5@fanstore.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'balance' => 250.00,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
