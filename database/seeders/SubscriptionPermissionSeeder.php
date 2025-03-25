<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\SubscriptionPermission;
use App\Models\SubscriptionPlan;

class SubscriptionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wyłączam tymczasowo foreign key constraints
        Schema::disableForeignKeyConstraints();
        
        // Wyczyść istniejące dane
        DB::table('subscription_permissions')->truncate();
        DB::table('subscription_plan_permission')->truncate();
        
        // Włączam z powrotem foreign key constraints
        Schema::enableForeignKeyConstraints();

        // Definiowanie uprawnień dla subskrypcji
        $permissions = [
            // Moduł finansowy
            [
                'name' => 'Moduł finansowy',
                'code' => 'finance_module',
                'description' => 'Dostęp do podstawowych funkcji finansowych, faktur i płatności',
                'category' => 'modules',
                'feature_flag' => 'finance_module'
            ],
            
            // Moduł magazynowy
            [
                'name' => 'Zarządzanie magazynem',
                'code' => 'warehouse_module',
                'description' => 'Dostęp do modułu magazynowego i zarządzania produktami',
                'category' => 'modules',
                'feature_flag' => 'warehouse_module'
            ],
            
            // Moduł umów
            [
                'name' => 'Zarządzanie umowami',
                'code' => 'contracts_module',
                'description' => 'Dostęp do modułu umów i zarządzania dokumentami umownymi',
                'category' => 'modules',
                'feature_flag' => 'contracts_module'
            ],
            
            // Kosztorysy
            [
                'name' => 'Kosztorysy i wyceny',
                'code' => 'estimates',
                'description' => 'Tworzenie i zarządzanie kosztorysami oraz wycenami',
                'category' => 'features',
                'feature_flag' => 'estimates'
            ],
            
            // Raporty zaawansowane
            [
                'name' => 'Raporty zaawansowane',
                'code' => 'advanced_reports',
                'description' => 'Dostęp do zaawansowanych raportów i analiz',
                'category' => 'features',
                'feature_flag' => 'advanced_reports'
            ],
            
            // Dostęp do API
            [
                'name' => 'Dostęp do API',
                'code' => 'api_access',
                'description' => 'Dostęp do interfejsu API dla integracji zewnętrznych',
                'category' => 'features',
                'feature_flag' => 'api_access'
            ],
            
            // Limit faktur
            [
                'name' => 'Limit faktur',
                'code' => 'max_invoices',
                'description' => 'Maksymalna liczba faktur, którą można utworzyć',
                'category' => 'limits',
                'feature_flag' => null
            ],
            
            // Limit produktów
            [
                'name' => 'Limit produktów',
                'code' => 'max_products',
                'description' => 'Maksymalna liczba produktów, którą można dodać',
                'category' => 'limits',
                'feature_flag' => null
            ],
            
            // Limit kontrahentów
            [
                'name' => 'Limit kontrahentów',
                'code' => 'max_contractors',
                'description' => 'Maksymalna liczba kontrahentów, którą można dodać',
                'category' => 'limits',
                'feature_flag' => null
            ],
            
            // Dokumenty elektroniczne
            [
                'name' => 'Dokumenty elektroniczne',
                'code' => 'e_documents',
                'description' => 'Możliwość tworzenia i przechowywania dokumentów elektronicznych',
                'category' => 'features',
                'feature_flag' => 'e_documents'
            ],
            
            // Powiadomienia SMS
            [
                'name' => 'Powiadomienia SMS',
                'code' => 'sms_notifications',
                'description' => 'Możliwość wysyłania powiadomień SMS do klientów',
                'category' => 'features',
                'feature_flag' => 'sms_notifications'
            ],
            
            // Integracja z bankami
            [
                'name' => 'Integracja bankowa',
                'code' => 'bank_integration',
                'description' => 'Integracja z systemami bankowymi do automatyzacji płatności',
                'category' => 'integrations',
                'feature_flag' => 'bank_integration'
            ],
            
            // Integracja z e-commerce
            [
                'name' => 'Integracja e-commerce',
                'code' => 'ecommerce_integration',
                'description' => 'Integracja z platformami e-commerce (WooCommerce, Shopify, itp.)',
                'category' => 'integrations',
                'feature_flag' => 'ecommerce_integration'
            ]
        ];

        // Dodaj uprawnienia
        foreach ($permissions as $permissionData) {
            SubscriptionPermission::create($permissionData);
        }

        // Przypisanie uprawnień do planów
        $plans = SubscriptionPlan::all();
        
        foreach ($plans as $plan) {
            $planPermissions = json_decode($plan->features, true);
            
            if (empty($planPermissions)) {
                continue;
            }
            
            $permissionIds = SubscriptionPermission::whereIn('code', $planPermissions)
                ->orWhere('category', 'limits')
                ->pluck('id');
            
            $pivotData = [];
            foreach ($permissionIds as $permissionId) {
                $permission = SubscriptionPermission::find($permissionId);
                
                // Dla limitów dodaj wartość z planu
                $value = null;
                if ($permission->category === 'limits') {
                    $code = $permission->code;
                    if (property_exists($plan, $code)) {
                        $value = $plan->$code;
                    }
                }
                
                $pivotData[$permissionId] = ['value' => $value];
            }
            
            $plan->permissions()->sync($pivotData);
        }
    }
}
