<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'role' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'company_name' => 'Suka-Suka Web',
            'phone' => '081234567890',
        ]);

        // Client 1
        User::create([
            'role' => 'client',
            'name' => 'Client One',
            'email' => 'client1@gmail.com',
            'password' => Hash::make('password123'),
            'company_name' => 'PT Client Satu',
            'phone' => '081111111111',
        ]);

        // Client 2
        User::create([
            'role' => 'client',
            'name' => 'Client Two',
            'email' => 'client2@gmail.com',
            'password' => Hash::make('password'),
            'company_name' => 'PT Client Dua',
            'phone' => '082222222222',
        ]);
    }
}
