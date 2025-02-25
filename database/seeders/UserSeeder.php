<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()
            ->firstOrCreate([
                'email' => 'admin@example.com',
            ], [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('secret'),
            ]);

    }
}
