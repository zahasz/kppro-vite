<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definicje uprawnień dla różnych modułów
        $permissions = [
            // Moduł użytkowników
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Moduł finansów
            'finances.view',
            'finances.create',
            'finances.edit',
            'finances.delete',
            
            // Moduł księgowości
            'accounting.view',
            'accounting.create',
            'accounting.edit',
            'accounting.delete',
            
            // Moduł faktur
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            
            // Moduł ofert
            'offers.view',
            'offers.create',
            'offers.edit',
            'offers.delete',
            
            // Moduł pracowników
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            
            // Moduł magazynu
            'warehouse.view',
            'warehouse.create',
            'warehouse.edit',
            'warehouse.delete',
            
            // Moduł kosztorysów
            'estimates.view',
            'estimates.create',
            'estimates.edit',
            'estimates.delete',
            
            // Moduł kontrahentów
            'contractors.view',
            'contractors.create',
            'contractors.edit',
            'contractors.delete',
            
            // Moduł raportów
            'reports.view',
            'reports.create',
            'reports.export',
            
            // Moduł ustawień
            'settings.view',
            'settings.edit',
        ];

        // Tworzenie ról
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Pełny dostęp do wszystkich funkcji systemu',
                'permissions' => $permissions,
                'is_system' => true
            ],
            [
                'name' => 'manager',
                'display_name' => 'Kierownik',
                'description' => 'Zarządzanie firmą i pracownikami',
                'permissions' => array_filter($permissions, function($permission) {
                    return !str_starts_with($permission, 'settings.');
                }),
                'is_system' => true
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Księgowy',
                'description' => 'Dostęp do modułów finansowych i księgowych',
                'permissions' => array_filter($permissions, function($permission) {
                    return str_starts_with($permission, 'finances.') || 
                           str_starts_with($permission, 'accounting.') || 
                           str_starts_with($permission, 'invoices.');
                }),
                'is_system' => true
            ],
            [
                'name' => 'employee',
                'display_name' => 'Pracownik',
                'description' => 'Podstawowy dostęp do systemu',
                'permissions' => array_filter($permissions, function($permission) {
                    return str_ends_with($permission, '.view') || 
                           str_starts_with($permission, 'offers.');
                }),
                'is_system' => true
            ]
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Tworzenie administratora systemu
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@kppro.pl',
            'password' => bcrypt('admin123'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'is_active' => true
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->id, [
            'assigned_at' => now(),
            'assigned_by' => $admin->id
        ]);

        // Tworzenie konta testowego
        $testUser = User::create([
            'name' => 'Konto Testowe',
            'email' => 'test@kppro.pl',
            'password' => bcrypt('Test123!'),
            'email_verified_at' => now(),
            'role' => 'manager',
            'is_active' => true,
            'language' => 'pl',
            'timezone' => 'Europe/Warsaw'
        ]);

        // Przypisanie roli managera i księgowego do konta testowego
        $managerRole = Role::where('name', 'manager')->first();
        $accountantRole = Role::where('name', 'accountant')->first();
        
        $testUser->roles()->attach([
            $managerRole->id => [
                'assigned_at' => now(),
                'assigned_by' => $admin->id
            ],
            $accountantRole->id => [
                'assigned_at' => now(),
                'assigned_by' => $admin->id
            ]
        ]);
    }
}
