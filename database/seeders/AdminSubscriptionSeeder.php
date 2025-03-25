<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Czyszczenie cache'u
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tworzenie ról (jeśli nie istnieją)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        // Uprawnienia dotyczące zarządzania subskrypcjami
        $subscriptionPermissions = [
            'subscriptions.view',         // Przeglądanie subskrypcji
            'subscriptions.create',       // Tworzenie subskrypcji
            'subscriptions.edit',         // Edycja subskrypcji
            'subscriptions.delete',       // Usuwanie subskrypcji
            'subscriptions.assign',       // Przypisywanie subskrypcji do użytkowników
            'subscription_plans.view',    // Przeglądanie planów subskrypcji
            'subscription_plans.create',  // Tworzenie planów subskrypcji
            'subscription_plans.edit',    // Edycja planów subskrypcji
            'subscription_plans.delete',  // Usuwanie planów subskrypcji
            'payments.view',              // Przeglądanie płatności
            'payments.process',           // Przetwarzanie płatności
            'payments.refund',            // Zwracanie płatności
            'subscription_reports.view',  // Raporty subskrypcji
        ];

        // Dodanie uprawnień
        foreach ($subscriptionPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Przypisanie uprawnień do roli administratora
        if ($adminRole) {
            foreach ($subscriptionPermissions as $permission) {
                $adminRole->givePermissionTo($permission);
            }
        }

        // Przypisanie uprawnień do roli superadmina (jeśli istnieje)
        if ($superAdminRole) {
            foreach ($subscriptionPermissions as $permission) {
                $superAdminRole->givePermissionTo($permission);
            }
        }

        // Informacja o postępie
        $this->command->info('Uprawnienia dotyczące zarządzania subskrypcjami zostały dodane.');
        $this->command->info('Przypisano uprawnienia do ról administratora i superadministratora.');
    }
}
