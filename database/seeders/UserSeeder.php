<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'email' => 'trungdungle@homies5.com',
            'password' => Hash::make('abcd1234'),
            'first_name' => 'Admin',
            'last_name' => 'Vip Pro',
            'age' => 30,
            'role' => 'Admin',
            'isVerify' => true,
        ]);
    }
}
