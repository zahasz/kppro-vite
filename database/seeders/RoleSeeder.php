<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tworzenie roli administratora
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );

        // Przypisanie roli administratora do pierwszego użytkownika
        $user = User::first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
