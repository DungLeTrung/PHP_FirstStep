<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'first_name' => 'Le',
            'last_name' => 'Dung',
            'age' => 22,
        ]);

        \App\Models\User::create([
            'first_name' => 'Hoang',
            'last_name' => 'Nam',
            'age' => 23,
        ]);

        \App\Models\User::create([
            'first_name' => 'Dieu',
            'last_name' => 'Linh',
            'age' => 19,
        ]);
    }
}
