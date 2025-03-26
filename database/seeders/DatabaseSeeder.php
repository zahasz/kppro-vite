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
            RoleAndPermissionSeeder::class,
            AdminPlanSeeder::class,
            ContractorSeeder::class,
            BudgetCategoriesSeeder::class,
            AdminSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
