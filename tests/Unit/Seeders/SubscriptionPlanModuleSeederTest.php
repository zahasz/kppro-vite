<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use App\Models\Module;
use App\Models\SubscriptionPlan;
use Database\Seeders\SubscriptionPlanModuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionPlanModuleSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Tworzenie modułów testowych
        $this->createModules();

        // Tworzenie planów subskrypcji
        $this->createSubscriptionPlans();
    }

    protected function createModules(): void
    {
        // Tworzymy wszystkie moduły, które są używane w seederze
        $modules = [
            ['name' => 'Panel główny', 'code' => 'dashboard', 'is_active' => true],
            ['name' => 'Faktury', 'code' => 'invoices', 'is_active' => true],
            ['name' => 'Kontrahenci', 'code' => 'contractors', 'is_active' => true],
            ['name' => 'Produkty', 'code' => 'products', 'is_active' => true],
            ['name' => 'Finanse', 'code' => 'finances', 'is_active' => true],
            ['name' => 'Wyceny', 'code' => 'estimates', 'is_active' => true],
            ['name' => 'Magazyn', 'code' => 'warehouse', 'is_active' => true],
            ['name' => 'Nieaktywny moduł', 'code' => 'inactive_module', 'is_active' => false]
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }

    protected function createSubscriptionPlans(): void
    {
        // Tworzymy plany subskrypcji używane w seederze
        SubscriptionPlan::create([
            'name' => 'Basic',
            'code' => 'basic',
            'description' => 'Plan podstawowy',
            'price' => 29.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'is_active' => true
        ]);

        SubscriptionPlan::create([
            'name' => 'Business',
            'code' => 'business',
            'description' => 'Plan biznesowy',
            'price' => 99.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'is_active' => true
        ]);

        SubscriptionPlan::create([
            'name' => 'Enterprise',
            'code' => 'enterprise',
            'description' => 'Plan enterprise',
            'price' => 199.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_assigns_correct_modules_to_basic_plan()
    {
        // Uruchomienie seedera
        $seeder = new SubscriptionPlanModuleSeeder();
        $seeder->run();

        // Pobranie planu basic
        $basicPlan = SubscriptionPlan::where('code', 'basic')->first();
        $this->assertNotNull($basicPlan);

        // Sprawdzenie, czy moduły zostały prawidłowo przypisane
        $expectedModules = ['dashboard', 'invoices', 'contractors', 'products'];
        foreach ($expectedModules as $moduleCode) {
            $this->assertTrue(
                $basicPlan->hasModuleAccess($moduleCode),
                "Plan Basic powinien mieć dostęp do modułu {$moduleCode}"
            );
        }

        // Sprawdzenie, czy plan nie ma dostępu do modułów, które nie powinny być przypisane
        $unexpectedModules = ['finances', 'estimates', 'warehouse'];
        foreach ($unexpectedModules as $moduleCode) {
            $this->assertFalse(
                $basicPlan->hasModuleAccess($moduleCode),
                "Plan Basic nie powinien mieć dostępu do modułu {$moduleCode}"
            );
        }

        // Sprawdzenie limitów dla modułów
        $invoiceLimitations = $basicPlan->getModuleLimitations('invoices');
        $this->assertNotNull($invoiceLimitations);
        $invoiceLimitations = json_decode($invoiceLimitations, true);
        $this->assertEquals(50, $invoiceLimitations['max_invoices']);
        $this->assertFalse($invoiceLimitations['export_allowed']);

        $contractorsLimitations = $basicPlan->getModuleLimitations('contractors');
        $this->assertNotNull($contractorsLimitations);
        $contractorsLimitations = json_decode($contractorsLimitations, true);
        $this->assertEquals(50, $contractorsLimitations['max_contractors']);

        $productsLimitations = $basicPlan->getModuleLimitations('products');
        $this->assertNotNull($productsLimitations);
        $productsLimitations = json_decode($productsLimitations, true);
        $this->assertEquals(100, $productsLimitations['max_products']);
    }

    /** @test */
    public function it_assigns_correct_modules_to_business_plan()
    {
        // Uruchomienie seedera
        $seeder = new SubscriptionPlanModuleSeeder();
        $seeder->run();

        // Pobranie planu business
        $businessPlan = SubscriptionPlan::where('code', 'business')->first();
        $this->assertNotNull($businessPlan);

        // Sprawdzenie, czy moduły zostały prawidłowo przypisane
        $expectedModules = ['dashboard', 'invoices', 'contractors', 'products', 'finances', 'estimates', 'warehouse'];
        foreach ($expectedModules as $moduleCode) {
            $this->assertTrue(
                $businessPlan->hasModuleAccess($moduleCode),
                "Plan Business powinien mieć dostęp do modułu {$moduleCode}"
            );
        }

        // Sprawdzenie limitów dla modułów
        $invoiceLimitations = $businessPlan->getModuleLimitations('invoices');
        $this->assertNotNull($invoiceLimitations);
        $invoiceLimitations = json_decode($invoiceLimitations, true);
        $this->assertEquals(500, $invoiceLimitations['max_invoices']);
        $this->assertTrue($invoiceLimitations['export_allowed']);

        $warehouseLimitations = $businessPlan->getModuleLimitations('warehouse');
        $this->assertNotNull($warehouseLimitations);
        $warehouseLimitations = json_decode($warehouseLimitations, true);
        $this->assertEquals(200, $warehouseLimitations['max_items']);
    }

    /** @test */
    public function it_assigns_all_active_modules_to_enterprise_plan()
    {
        // Uruchomienie seedera
        $seeder = new SubscriptionPlanModuleSeeder();
        $seeder->run();

        // Pobranie planu enterprise
        $enterprisePlan = SubscriptionPlan::where('code', 'enterprise')->first();
        $this->assertNotNull($enterprisePlan);

        // Pobranie wszystkich aktywnych modułów
        $activeModules = Module::where('is_active', true)->get();
        
        // Sprawdzenie, czy wszystkie aktywne moduły zostały przypisane do planu
        foreach ($activeModules as $module) {
            $this->assertTrue(
                $enterprisePlan->hasModuleAccess($module->code),
                "Plan Enterprise powinien mieć dostęp do modułu {$module->code}"
            );
        }

        // Sprawdzenie, czy nieaktywne moduły NIE są przypisane
        $inactiveModule = Module::where('code', 'inactive_module')->first();
        $this->assertFalse(
            $enterprisePlan->hasModuleAccess($inactiveModule->code),
            "Plan Enterprise nie powinien mieć dostępu do nieaktywnego modułu"
        );

        // Dla planu Enterprise nie powinno być żadnych ograniczeń
        $invoiceLimitations = $enterprisePlan->getModuleLimitations('invoices');
        $this->assertNull(json_decode($invoiceLimitations, true));
    }

    /** @test */
    public function it_handles_nonexistent_plans_gracefully()
    {
        // Usunięcie wszystkich planów subskrypcji
        SubscriptionPlan::query()->delete();

        // Uruchomienie seedera - nie powinno wywołać błędu
        $seeder = new SubscriptionPlanModuleSeeder();
        $seeder->run();

        // Sprawdzenie, czy seeder nie utworzył żadnych powiązań
        $this->assertEquals(0, \DB::table('subscription_plan_modules')->count());
    }

    /** @test */
    public function it_clears_existing_assignments_before_seeding()
    {
        // Utwórz ręcznie jakieś przypisanie
        $basicPlan = SubscriptionPlan::where('code', 'basic')->first();
        $module = Module::where('code', 'finances')->first();
        
        \DB::table('subscription_plan_modules')->insert([
            'subscription_plan_id' => $basicPlan->id,
            'module_id' => $module->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Sprawdź, czy przypisanie istnieje
        $this->assertTrue($basicPlan->hasModuleAccess('finances'));
        
        // Uruchom seeder
        $seeder = new SubscriptionPlanModuleSeeder();
        $seeder->run();
        
        // Pobierz odświeżony plan
        $basicPlan->refresh();
        
        // Sprawdź, czy poprzednie przypisanie zostało usunięte
        $this->assertFalse($basicPlan->hasModuleAccess('finances'));
        
        // Sprawdź, czy nowe przypisania zostały dodane
        $this->assertTrue($basicPlan->hasModuleAccess('invoices'));
    }
} 