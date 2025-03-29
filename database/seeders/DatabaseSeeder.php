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
            AdminPlanSeeder::class,          // Konfiguracja planów dla administratora
            ContractorSeeder::class,         // Przykładowi kontrahenci
            InvoiceSeeder::class,            // Przykładowe faktury
        ]);
    }
}
