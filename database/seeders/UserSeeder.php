<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@laundry.com',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Kasir
        User::create([
            'name' => 'Kasir Utama',
            'email' => 'kasir@laundry.com',
            'phone' => '081234567891',
            'address' => 'Jl. Kasir No. 1',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);

        // Pelanggan
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'phone' => '081234567892',
            'address' => 'Jl. Pelanggan No. 1',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@email.com',
            'phone' => '081234567893',
            'address' => 'Jl. Pelanggan No. 2',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);
    }
}
