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

            // Magazyn - Materiały
            'warehouse.materials.view',
            'warehouse.materials.create',
            'warehouse.materials.edit',
            'warehouse.materials.delete',
            
            // Magazyn - Sprzęt
            'warehouse.equipment.view',
            'warehouse.equipment.create',
            'warehouse.equipment.edit',
            'warehouse.equipment.delete',
            
            // Magazyn - Narzędzia
            'warehouse.tools.view',
            'warehouse.tools.create',
            'warehouse.tools.edit',
            'warehouse.tools.delete',
            
            // Magazyn - Garaż
            'warehouse.garage.view',
            'warehouse.garage.create',
            'warehouse.garage.edit',
            'warehouse.garage.delete',

            // Finanse
            'finances.view',
            'finances.create',
            'finances.edit',
            'finances.delete',
            'finances.approve', // Zatwierdzanie transakcji
            'finances.reports', // Generowanie raportów

            // Budżet
            'budget.view',
            'budget.create',
            'budget.edit',
            'budget.delete',
            'budget.approve', // Zatwierdzanie budżetu

            // Faktury
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'invoices.approve', // Zatwierdzanie faktur

            // Kontrahenci
            'contractors.view',
            'contractors.create',
            'contractors.edit',
            'contractors.delete',

            // Umowy
            'contracts.view',
            'contracts.create',
            'contracts.edit',
            'contracts.delete',
            'contracts.approve', // Zatwierdzanie umów

            // Kosztorysy
            'estimates.view',
            'estimates.create',
            'estimates.edit',
            'estimates.delete',
            'estimates.approve', // Zatwierdzanie kosztorysów

            // Zadania
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
            'tasks.assign', // Przydzielanie zadań
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
            'admin' => $permissions, // Administrator ma wszystkie uprawnienia

            'manager' => [ // Kierownik
                'users.view',
                'users.create',
                'users.edit',
                'roles.view',
                'settings.view',
                'warehouse.materials.view',
                'warehouse.materials.create',
                'warehouse.materials.edit',
                'warehouse.equipment.view',
                'warehouse.equipment.create',
                'warehouse.equipment.edit',
                'warehouse.tools.view',
                'warehouse.tools.create',
                'warehouse.tools.edit',
                'warehouse.garage.view',
                'warehouse.garage.create',
                'warehouse.garage.edit',
                'finances.view',
                'finances.create',
                'finances.edit',
                'finances.reports',
                'budget.view',
                'budget.create',
                'budget.edit',
                'invoices.view',
                'invoices.create',
                'invoices.edit',
                'contractors.view',
                'contractors.create',
                'contractors.edit',
                'contracts.view',
                'contracts.create',
                'contracts.edit',
                'estimates.view',
                'estimates.create',
                'estimates.edit',
                'tasks.view',
                'tasks.create',
                'tasks.edit',
                'tasks.assign',
            ],

            'accountant' => [ // Księgowy
                'finances.view',
                'finances.create',
                'finances.edit',
                'finances.approve',
                'finances.reports',
                'budget.view',
                'budget.create',
                'budget.edit',
                'budget.approve',
                'invoices.view',
                'invoices.create',
                'invoices.edit',
                'invoices.approve',
                'contractors.view',
                'contractors.create',
                'contractors.edit',
            ],

            'warehouse_keeper' => [ // Magazynier
                'warehouse.materials.view',
                'warehouse.materials.create',
                'warehouse.materials.edit',
                'warehouse.equipment.view',
                'warehouse.equipment.create',
                'warehouse.equipment.edit',
                'warehouse.tools.view',
                'warehouse.tools.create',
                'warehouse.tools.edit',
                'warehouse.garage.view',
                'warehouse.garage.create',
                'warehouse.garage.edit',
            ],

            'employee' => [ // Pracownik
                'warehouse.materials.view',
                'warehouse.equipment.view',
                'warehouse.tools.view',
                'warehouse.garage.view',
                'tasks.view',
                'tasks.edit',
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
