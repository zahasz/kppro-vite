<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprawdzamy, czy tabela istnieje
        if (!Schema::hasTable('modules')) {
            return;
        }

        // Wyłączamy tymczasowo foreign key constraints
        Schema::disableForeignKeyConstraints();
        
        // Czyścimy tabelę
        DB::table('modules')->truncate();
        
        // Włączamy z powrotem foreign key constraints
        Schema::enableForeignKeyConstraints();

        // Definiujemy moduły aplikacji
        $modules = [
            [
                'name' => 'Dashboard',
                'code' => 'dashboard',
                'description' => 'Pulpit główny aplikacji z podstawowymi statystykami',
                'is_active' => true
            ],
            [
                'name' => 'Faktury',
                'code' => 'invoices',
                'description' => 'Moduł do zarządzania fakturami',
                'is_active' => true
            ],
            [
                'name' => 'Magazyn',
                'code' => 'warehouse',
                'description' => 'Moduł do zarządzania magazynem',
                'is_active' => true
            ],
            [
                'name' => 'Finanse',
                'code' => 'finances',
                'description' => 'Moduł do zarządzania finansami',
                'is_active' => true
            ],
            [
                'name' => 'Kontrahenci',
                'code' => 'contractors',
                'description' => 'Moduł do zarządzania kontrahentami',
                'is_active' => true
            ],
            [
                'name' => 'Produkty',
                'code' => 'products',
                'description' => 'Moduł do zarządzania produktami',
                'is_active' => true
            ],
            [
                'name' => 'Kosztorysy',
                'code' => 'estimates',
                'description' => 'Moduł do tworzenia kosztorysów',
                'is_active' => true
            ],
            [
                'name' => 'Umowy',
                'code' => 'contracts',
                'description' => 'Moduł do zarządzania umowami',
                'is_active' => true
            ],
            [
                'name' => 'Zadania',
                'code' => 'tasks',
                'description' => 'Moduł do zarządzania zadaniami',
                'is_active' => true
            ],
            [
                'name' => 'Raporty',
                'code' => 'reports',
                'description' => 'Moduł do generowania raportów',
                'is_active' => true
            ],
            [
                'name' => 'API',
                'code' => 'api',
                'description' => 'Dostęp do API aplikacji',
                'is_active' => true
            ],
            [
                'name' => 'Ustawienia',
                'code' => 'settings',
                'description' => 'Moduł do zarządzania ustawieniami aplikacji',
                'is_active' => true
            ],
        ];

        // Dodajemy moduły do bazy danych
        foreach ($modules as $moduleData) {
            Module::create($moduleData);
        }
    }
}
