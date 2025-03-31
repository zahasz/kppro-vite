<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class StartupSeeder extends Seeder
{
    /**
     * Główny seeder do inicjalizacji systemu od zera.
     * Zawiera wszystkie niezbędne elementy do uruchomienia aplikacji.
     */
    public function run(): void
    {
        $this->command->info('Rozpoczynam inicjalizację systemu...');

        // 1. Konfiguracja ról i uprawnień
        $this->command->info('Konfiguruję role i uprawnienia...');
        $this->call(RoleAndPermissionSeeder::class);
        
        // 2. Konfiguracja konta administratora
        $this->command->info('Tworzę konto administratora...');
        $this->call(AdminSeeder::class);
        
        // 3. Konfiguracja planów subskrypcji
        $this->command->info('Konfiguruję plany subskrypcji...');
        $this->call(SubscriptionPlansSeeder::class);
        
        // 4. Przypisanie subskrypcji administratorowi
        $this->command->info('Przypisuję subskrypcję administratorowi...');
        $this->call(AdminSubscriptionSeeder::class);
        
        // 5. Konfiguracja modułów
        $this->command->info('Konfiguruję moduły aplikacji...');
        $this->call(ModuleSeeder::class);
        
        // 6. Przypisanie modułów do planów subskrypcji
        $this->command->info('Przypisuję moduły do planów...');
        $this->call(SubscriptionPlanModuleSeeder::class);
        
        // 7. Ustawienia płatności
        $this->command->info('Konfiguruję ustawienia płatności...');
        $this->call(PaymentSettingsSeeder::class);
        
        // 8. Ustawienia fakturowania
        $this->command->info('Konfiguruję ustawienia fakturowania...');
        $this->call(BillingSettingsSeeder::class);
        
        // Wyczyszczenie cache
        $this->command->info('Czyszczę cache aplikacji...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        $this->command->info('Inicjalizacja systemu zakończona pomyślnie!');
        $this->command->info('Dane logowania administratora:');
        $this->command->info('Email: admin@kppro.pl');
        $this->command->info('Hasło: admin123');
    }
} 