<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Wywołanie seederów
        $this->call([
            SetupSystemSeeder::class,        // Główny seeder konfigurujący system
            RoleAndPermissionSeeder::class,  // Role i uprawnienia
            AdminSeeder::class,              // Konfiguracja administratora
            ContractorSeeder::class,         // Przykładowi kontrahenci
            InvoiceSeeder::class,            // Przykładowe faktury
            
            // Moduł subskrypcji
            SubscriptionPermissionSeeder::class, // Uprawnienia dla subskrypcji
            SubscriptionSeeder::class,       // Plany i przypisanie subskrypcji
            
            // Moduł uprawnień do modułów
            ModuleSeeder::class,             // Moduły aplikacji
            SubscriptionPlanModuleSeeder::class, // Przypisanie modułów do planów
        ]);
    }
}
