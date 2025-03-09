<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Czyszczenie cache'u
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tworzenie uprawnień
        $permissions = [
            // Użytkownicy
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Role
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // Ustawienia
            'settings.view',
            'settings.edit',
        ];

        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $createdPermissions[] = Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Tworzenie ról
        $roles = [
            'admin' => $permissions,
            'manager' => [
                'users.view',
                'users.create',
                'users.edit',
                'roles.view',
                'settings.view',
            ],
            'user' => [
                'users.view',
                'settings.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
            $role->syncPermissions($rolePermissions);
        }

        // Tworzenie administratora systemu
        $admin = User::firstOrCreate(
            ['email' => 'admin@kppro.pl'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123'),
                'email_verified_at' => now(),
                'is_active' => true
            ]
        );

        $admin->assignRole('admin');

        // Tworzenie konta testowego
        $testUser = User::firstOrCreate(
            ['email' => 'test@kppro.pl'],
            [
                'name' => 'Konto Testowe',
                'password' => bcrypt('Test123!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'language' => 'pl',
                'timezone' => 'Europe/Warsaw'
            ]
        );

        $testUser->assignRole('manager');
    }
}
