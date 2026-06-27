<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username'    => 'admin',
                'password'    => Hash::make('admin123'),
                'role_id'     => 1,
                'status'      => 1,
                'create_date' => now(),
            ],
            [
                'username'    => 'gerente',
                'password'    => Hash::make('gerente123'),
                'role_id'     => 2,
                'status'      => 1,
                'create_date' => now(),
            ],
            [
                'username'    => 'mecanico',
                'password'    => Hash::make('mecanico123'),
                'role_id'     => 3,
                'status'      => 1,
                'create_date' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['username' => $user['username']], $user);
        }
    }
}
