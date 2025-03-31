<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\Module;
use App\Services\ModulePermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class SubscriptionPlanModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprawdzamy, czy tabele istnieją
        if (!Schema::hasTable('modules') || !Schema::hasTable('subscription_plans') || !Schema::hasTable('subscription_plan_modules')) {
            return;
        }

        // Wyłączamy tymczasowo foreign key constraints
        Schema::disableForeignKeyConstraints();
        
        // Czyścimy tabelę powiązań
        DB::table('subscription_plan_modules')->truncate();
        
        // Włączamy z powrotem foreign key constraints
        Schema::enableForeignKeyConstraints();

        // Pobierz plany subskrypcji
        $basicPlan = SubscriptionPlan::where('code', 'basic')->first();
        $businessPlan = SubscriptionPlan::where('code', 'business')->first();
        $enterprisePlan = SubscriptionPlan::where('code', 'enterprise')->first();
        
        if (!$basicPlan && !$businessPlan && !$enterprisePlan) {
            Log::warning('Brak planów subskrypcji do przypisania modułów');
            return;
        }
        
        $modulePermissionService = app(ModulePermissionService::class);
        
        // Przypisz moduły do planu Basic
        if ($basicPlan) {
            // Moduły dla planu Basic
            $modules = [
                'dashboard',
                'invoices',
                'contractors',
                'products'
            ];
            
            // Limity dla modułów
            $limitations = [
                'invoices' => ['max_invoices' => 50, 'export_allowed' => false],
                'contractors' => ['max_contractors' => 50],
                'products' => ['max_products' => 100]
            ];
            
            $modulePermissionService->assignModulesToPlan($basicPlan, $modules, $limitations);
        }
        
        // Przypisz moduły do planu Business
        if ($businessPlan) {
            // Moduły dla planu Business
            $modules = [
                'dashboard',
                'invoices',
                'finances',
                'contractors',
                'products',
                'estimates',
                'warehouse'
            ];
            
            // Limity dla modułów
            $limitations = [
                'invoices' => ['max_invoices' => 500, 'export_allowed' => true],
                'contractors' => ['max_contractors' => 250],
                'products' => ['max_products' => 500],
                'warehouse' => ['max_items' => 200]
            ];
            
            $modulePermissionService->assignModulesToPlan($businessPlan, $modules, $limitations);
        }
        
        // Przypisz moduły do planu Enterprise (wszystkie moduły)
        if ($enterprisePlan) {
            // Pobierz wszystkie kody modułów
            $modules = Module::where('is_active', true)->pluck('code')->toArray();
            
            // Dla planu Enterprise nie ma limitów - nieograniczony dostęp
            $limitations = [];
            
            $modulePermissionService->assignModulesToPlan($enterprisePlan, $modules, $limitations);
        }
    }
}
