<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admindapur@resto.test'],
            [
                'name' => 'Admin Dapur',
                'password' => Hash::make('password'),
                'role' => 'admin_dapur',
            ]
        );

        User::firstOrCreate(
            ['email' => 'operator@resto.test'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]
        );
    }
}
