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
        // Sprawdzamy, czy tabele istnieją
        if (!Schema::hasTable('subscription_permissions') || !Schema::hasTable('subscription_permission_plan')) {
            return;
        }

        // Wyłączamy tymczasowo foreign key constraints
        Schema::disableForeignKeyConstraints();
        
        // Czyścimy tabele
        DB::table('subscription_permission_plan')->truncate();
        DB::table('subscription_permissions')->truncate();
        
        // Włączamy z powrotem foreign key constraints
        Schema::enableForeignKeyConstraints();

        // Definiujemy uprawnienia subskrypcji
        $permissions = [
            // Moduły podstawowe
            [
                'name' => 'Moduł finansowy',
                'code' => 'finance_module',
                'description' => 'Dostęp do podstawowych funkcji finansowych, faktur i płatności',
                'category' => 'modules',
                'feature_flag' => 'finance_module'
            ],
            [
                'name' => 'Zarządzanie magazynem',
                'code' => 'warehouse_module',
                'description' => 'Dostęp do modułu magazynowego i zarządzania produktami',
                'category' => 'modules',
                'feature_flag' => 'warehouse_module'
            ],
            [
                'name' => 'Zarządzanie umowami',
                'code' => 'contracts_module',
                'description' => 'Dostęp do modułu umów i zarządzania dokumentami umownymi',
                'category' => 'modules',
                'feature_flag' => 'contracts_module'
            ],
            
            // Funkcje fakturowania
            [
                'name' => 'Wystawianie faktur',
                'code' => 'invoice_generation',
                'description' => 'Możliwość tworzenia i wystawiania faktur',
                'category' => 'invoicing',
                'feature_flag' => 'invoice_generation'
            ],
            [
                'name' => 'Zaawansowane fakturowanie',
                'code' => 'advanced_invoicing',
                'description' => 'Dostęp do zaawansowanych funkcji fakturowania',
                'category' => 'invoicing',
                'feature_flag' => 'advanced_invoicing'
            ],
            [
                'name' => 'Faktury cykliczne',
                'code' => 'recurring_invoices',
                'description' => 'Możliwość tworzenia i zarządzania fakturami cyklicznymi',
                'category' => 'invoicing',
                'feature_flag' => 'recurring_invoices'
            ],
            [
                'name' => 'Wysyłanie faktur e-mailem',
                'code' => 'email_invoices',
                'description' => 'Możliwość automatycznego wysyłania faktur e-mailem',
                'category' => 'invoicing',
                'feature_flag' => 'email_invoices'
            ],
            
            // Raporty
            [
                'name' => 'Podstawowe raporty',
                'code' => 'simple_reports',
                'description' => 'Dostęp do podstawowych raportów i zestawień',
                'category' => 'reporting',
                'feature_flag' => 'simple_reports'
            ],
            [
                'name' => 'Raporty zaawansowane',
                'code' => 'advanced_reports',
                'description' => 'Dostęp do zaawansowanych raportów i analiz',
                'category' => 'reporting',
                'feature_flag' => 'advanced_reports'
            ],
            
            // Funkcje klienckie
            [
                'name' => 'Portal dla klientów',
                'code' => 'client_portal',
                'description' => 'Dostęp do portalu klienckiego dla kontrahentów',
                'category' => 'client',
                'feature_flag' => 'client_portal'
            ],
            [
                'name' => 'Przypomnienia o płatnościach',
                'code' => 'payment_reminders',
                'description' => 'Automatyczne przypomnienia o zaległych płatnościach',
                'category' => 'client',
                'feature_flag' => 'payment_reminders'
            ],
            
            // Dokumenty
            [
                'name' => 'Szablony dokumentów',
                'code' => 'document_templates',
                'description' => 'Możliwość tworzenia i zarządzania szablonami dokumentów',
                'category' => 'documents',
                'feature_flag' => 'document_templates'
            ],
            [
                'name' => 'Kosztorysy i wyceny',
                'code' => 'estimates',
                'description' => 'Tworzenie i zarządzanie kosztorysami oraz wycenami',
                'category' => 'documents',
                'feature_flag' => 'estimates'
            ],
            [
                'name' => 'Dokumenty elektroniczne',
                'code' => 'e_documents',
                'description' => 'Możliwość tworzenia i przechowywania dokumentów elektronicznych',
                'category' => 'documents',
                'feature_flag' => 'e_documents'
            ],
            [
                'name' => 'Zarządzanie dokumentami',
                'code' => 'document_management',
                'description' => 'Zaawansowany system zarządzania dokumentami',
                'category' => 'documents',
                'feature_flag' => 'document_management'
            ],
            
            // Zarządzanie magazynem - rozszerzone
            [
                'name' => 'Zaawansowane zarządzanie magazynem',
                'code' => 'advanced_inventory',
                'description' => 'Zaawansowane funkcje zarządzania zapasami',
                'category' => 'warehouse',
                'feature_flag' => 'advanced_inventory'
            ],
            [
                'name' => 'Obsługa wielu magazynów',
                'code' => 'multi_warehouse',
                'description' => 'Możliwość obsługi wielu magazynów w różnych lokalizacjach',
                'category' => 'warehouse',
                'feature_flag' => 'multi_warehouse'
            ],
            [
                'name' => 'Zarządzanie produkcją',
                'code' => 'production_management',
                'description' => 'Narzędzia do zarządzania produkcją i przetwarzaniem zapasów',
                'category' => 'warehouse',
                'feature_flag' => 'production_management'
            ],
            
            // Integracje
            [
                'name' => 'Integracja bankowa',
                'code' => 'bank_integration',
                'description' => 'Integracja z systemami bankowymi do automatyzacji płatności',
                'category' => 'integrations',
                'feature_flag' => 'bank_integration'
            ],
            [
                'name' => 'Integracja z księgowością',
                'code' => 'accounting_integration',
                'description' => 'Integracja z zewnętrznymi systemami księgowymi',
                'category' => 'integrations',
                'feature_flag' => 'accounting_integration'
            ],
            [
                'name' => 'Integracja e-commerce',
                'code' => 'ecommerce_integration',
                'description' => 'Integracja z platformami e-commerce (WooCommerce, Shopify, itp.)',
                'category' => 'integrations',
                'feature_flag' => 'ecommerce_integration'
            ],
            
            // API i komunikacja
            [
                'name' => 'Podstawowy dostęp do API',
                'code' => 'basic_api_access',
                'description' => 'Ograniczony dostęp do interfejsu API dla integracji zewnętrznych',
                'category' => 'api',
                'feature_flag' => 'basic_api_access'
            ],
            [
                'name' => 'Pełny dostęp do API',
                'code' => 'full_api_access',
                'description' => 'Pełny dostęp do wszystkich funkcji API dla integracji zewnętrznych',
                'category' => 'api',
                'feature_flag' => 'full_api_access'
            ],
            [
                'name' => 'Powiadomienia SMS',
                'code' => 'sms_notifications',
                'description' => 'Możliwość wysyłania powiadomień SMS do klientów',
                'category' => 'communications',
                'feature_flag' => 'sms_notifications'
            ],
            
            // Funkcje Enterprise
            [
                'name' => 'Funkcje CRM',
                'code' => 'crm_features',
                'description' => 'Funkcje zarządzania relacjami z klientami',
                'category' => 'enterprise',
                'feature_flag' => 'crm_features'
            ],
            [
                'name' => 'Dostęp dla wielu użytkowników',
                'code' => 'multi_user_access',
                'description' => 'Dostęp dla wielu użytkowników z różnymi uprawnieniami',
                'category' => 'enterprise',
                'feature_flag' => 'multi_user_access'
            ],
            [
                'name' => 'Ścieżki zatwierdzania',
                'code' => 'approval_workflows',
                'description' => 'Zaawansowane ścieżki zatwierdzania dla dokumentów i procesów',
                'category' => 'enterprise',
                'feature_flag' => 'approval_workflows'
            ],
            [
                'name' => 'Niestandardowe role',
                'code' => 'custom_roles',
                'description' => 'Możliwość tworzenia niestandardowych ról i uprawnień',
                'category' => 'enterprise',
                'feature_flag' => 'custom_roles'
            ],
            [
                'name' => 'Logi aktywności',
                'code' => 'activity_logs',
                'description' => 'Szczegółowe logi aktywności użytkowników',
                'category' => 'enterprise',
                'feature_flag' => 'activity_logs'
            ],
            [
                'name' => 'Priorytetowe wsparcie',
                'code' => 'priority_support',
                'description' => 'Dostęp do priorytetowego wsparcia technicznego',
                'category' => 'support',
                'feature_flag' => 'priority_support'
            ],
            
            // Limity
            [
                'name' => 'Limit faktur',
                'code' => 'max_invoices',
                'description' => 'Maksymalna liczba faktur, którą można utworzyć',
                'category' => 'limits',
                'feature_flag' => null
            ],
            [
                'name' => 'Limit produktów',
                'code' => 'max_products',
                'description' => 'Maksymalna liczba produktów, którą można dodać',
                'category' => 'limits',
                'feature_flag' => null
            ],
            [
                'name' => 'Limit kontrahentów',
                'code' => 'max_clients',
                'description' => 'Maksymalna liczba kontrahentów, którą można dodać',
                'category' => 'limits',
                'feature_flag' => null
            ],
        ];

        // Dodaj uprawnienia
        foreach ($permissions as $permissionData) {
            SubscriptionPermission::create($permissionData);
        }

        // Przypisanie uprawnień do planów
        $basicPlan = SubscriptionPlan::where('code', 'basic')->first();
        $businessPlan = SubscriptionPlan::where('code', 'business')->first();
        $enterprisePlan = SubscriptionPlan::where('code', 'enterprise')->first();
        
        if ($basicPlan) {
            // Uprawnienia dla planu Basic
            $basicPermissions = [
                'finance_module',
                'invoice_generation',
                'simple_reports',
                'email_invoices',
                'max_invoices',
                'max_products',
                'max_clients'
            ];
            
            foreach ($basicPermissions as $permCode) {
                $permission = SubscriptionPermission::where('code', $permCode)->first();
                if ($permission) {
                    $basicPlan->permissions()->attach($permission->id);
                }
            }
        }
        
        if ($businessPlan) {
            // Uprawnienia dla planu Business
            $businessPermissions = [
                'finance_module',
                'warehouse_module',
                'estimates',
                'invoice_generation',
                'advanced_invoicing',
                'recurring_invoices',
                'email_invoices',
                'simple_reports',
                'client_portal',
                'payment_reminders',
                'document_templates',
                'bank_integration',
                'basic_api_access',
                'max_invoices',
                'max_products',
                'max_clients'
            ];
            
            foreach ($businessPermissions as $permCode) {
                $permission = SubscriptionPermission::where('code', $permCode)->first();
                if ($permission) {
                    $businessPlan->permissions()->attach($permission->id);
                }
            }
        }
        
        if ($enterprisePlan) {
            // Uprawnienia dla planu Enterprise - dostęp do wszystkiego
            $permissions = SubscriptionPermission::all();
            foreach ($permissions as $permission) {
                $enterprisePlan->permissions()->attach($permission->id);
            }
        }
    }
}
