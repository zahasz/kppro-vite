<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kppro.pl',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'is_active' => true,
            'language' => 'pl',
            'timezone' => 'Europe/Warsaw'
        ])->assignRole('admin');
    }
}
