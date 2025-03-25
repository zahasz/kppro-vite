<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Wykonaj seed danych.
     */
    public function run(): void
    {
        // Ustaw datę dla tworzenia planów, aby były spójne
        $now = Carbon::now();
        
        // 1. Plan Darmowy
        SubscriptionPlan::create([
            'name' => 'Darmowy',
            'code' => 'free',
            'description' => 'Podstawowa funkcjonalność dla małych firm. Idealne rozwiązanie do rozpoczęcia pracy z systemem.',
            'price' => 0,
            'billing_period' => 'lifetime',
            'is_active' => true,
            'max_invoices' => 10,
            'max_products' => 20,
            'max_contractors' => 15,
            'features' => [
                'invoices_basic',
                'contractors_basic',
                'products_basic'
            ],
            'display_order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        // 2. Plan Standard
        SubscriptionPlan::create([
            'name' => 'Standard',
            'code' => 'standard',
            'description' => 'Rozbudowane funkcje dla rozwijających się firm. Zawiera moduł fakturowania i magazynowy.',
            'price' => 49.00,
            'billing_period' => 'monthly',
            'is_active' => true,
            'max_invoices' => 100,
            'max_products' => 200,
            'max_contractors' => 100,
            'features' => [
                'invoices_advanced',
                'contractors_advanced',
                'products_advanced',
                'warehouse_basic',
                'financial_basic'
            ],
            'display_order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        // 3. Plan Premium
        SubscriptionPlan::create([
            'name' => 'Premium',
            'code' => 'premium',
            'description' => 'Pełny dostęp do wszystkich funkcji dla średnich i dużych firm. Idealny dla firm z zaawansowanymi potrzebami.',
            'price' => 99.00,
            'billing_period' => 'monthly',
            'is_active' => true,
            'max_invoices' => 1000,
            'max_products' => 2000,
            'max_contractors' => 1000,
            'features' => [
                'invoices_advanced',
                'contractors_advanced',
                'products_advanced',
                'warehouse_advanced',
                'financial_advanced',
                'reports_advanced',
                'contracts',
                'estimates',
                'api_access'
            ],
            'display_order' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        // 4. Plan Premium Roczny
        SubscriptionPlan::create([
            'name' => 'Premium Roczny',
            'code' => 'premium_yearly',
            'description' => 'Plan Premium z rabatem rocznym. Oszczędź 20% w porównaniu do rozliczenia miesięcznego.',
            'price' => 948.00, // 99 * 12 * 0.8 = 948 (20% zniżki w opłacie rocznej)
            'billing_period' => 'yearly',
            'is_active' => true,
            'max_invoices' => 1000,
            'max_products' => 2000,
            'max_contractors' => 1000,
            'features' => [
                'invoices_advanced',
                'contractors_advanced',
                'products_advanced',
                'warehouse_advanced',
                'financial_advanced',
                'reports_advanced',
                'contracts',
                'estimates',
                'api_access'
            ],
            'display_order' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
