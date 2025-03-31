<?php

namespace Tests\Feature\Subscription;

use Tests\TestCase;
use App\Models\User;
use App\Models\Module;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\ModulePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class UserSubscriptionModuleAccessTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $modulePermissionService;
    protected $user;
    protected $basicPlan;
    protected $businessPlan;
    protected $enterprisePlan;

    protected function setUp(): void
    {
        parent::setUp();

        // Inicjalizacja serwisu
        $this->modulePermissionService = app(ModulePermissionService::class);

        // Tworzenie użytkownika testowego
        $this->user = User::factory()->create();

        // Tworzenie modułów testowych
        Module::create([
            'name' => 'Panel główny',
            'code' => 'dashboard',
            'description' => 'Panel główny systemu',
            'is_active' => true
        ]);

        Module::create([
            'name' => 'Faktury',
            'code' => 'invoices',
            'description' => 'Moduł do zarządzania fakturami',
            'is_active' => true
        ]);

        Module::create([
            'name' => 'Kontrahenci',
            'code' => 'contractors',
            'description' => 'Moduł do zarządzania kontrahentami',
            'is_active' => true
        ]);

        Module::create([
            'name' => 'Produkty',
            'code' => 'products',
            'description' => 'Moduł do zarządzania produktami',
            'is_active' => true
        ]);

        Module::create([
            'name' => 'Finanse',
            'code' => 'finances',
            'description' => 'Moduł do zarządzania finansami',
            'is_active' => true
        ]);

        Module::create([
            'name' => 'Wyceny',
            'code' => 'estimates',
            'description' => 'Moduł do zarządzania wycenami',
            'is_active' => true
        ]);

        Module::create([
            'name' => 'Magazyn',
            'code' => 'warehouse',
            'description' => 'Moduł do zarządzania magazynem',
            'is_active' => true
        ]);

        // Tworzenie planów subskrypcji
        $this->basicPlan = SubscriptionPlan::create([
            'name' => 'Basic',
            'code' => 'basic',
            'description' => 'Plan podstawowy',
            'price' => 29.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['invoices', 'contractors'],
            'is_active' => true,
            'display_order' => 1
        ]);

        $this->businessPlan = SubscriptionPlan::create([
            'name' => 'Business',
            'code' => 'business',
            'description' => 'Plan biznesowy',
            'price' => 99.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['invoices', 'contractors', 'products', 'finances', 'estimates'],
            'is_active' => true,
            'display_order' => 2
        ]);

        $this->enterprisePlan = SubscriptionPlan::create([
            'name' => 'Enterprise',
            'code' => 'enterprise',
            'description' => 'Plan enterprise',
            'price' => 199.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['invoices', 'contractors', 'products', 'finances', 'estimates', 'warehouse'],
            'is_active' => true,
            'display_order' => 3
        ]);

        // Przypisanie modułów do planów
        $this->assignModulesToPlans();
    }

    protected function assignModulesToPlans()
    {
        // Przypisanie modułów do planu Basic
        $this->modulePermissionService->assignModulesToPlan(
            $this->basicPlan,
            ['dashboard', 'invoices', 'contractors', 'products'],
            [
                'invoices' => ['max_invoices' => 50, 'export_allowed' => false],
                'contractors' => ['max_contractors' => 50],
                'products' => ['max_products' => 100]
            ]
        );

        // Przypisanie modułów do planu Business
        $this->modulePermissionService->assignModulesToPlan(
            $this->businessPlan,
            ['dashboard', 'invoices', 'finances', 'contractors', 'products', 'estimates', 'warehouse'],
            [
                'invoices' => ['max_invoices' => 500, 'export_allowed' => true],
                'contractors' => ['max_contractors' => 250],
                'products' => ['max_products' => 500],
                'warehouse' => ['max_items' => 200]
            ]
        );

        // Przypisanie modułów do planu Enterprise
        $this->modulePermissionService->assignModulesToPlan(
            $this->enterprisePlan,
            ['dashboard', 'invoices', 'finances', 'contractors', 'products', 'estimates', 'warehouse'],
            []
        );
    }

    /** @test */
    public function user_without_subscription_has_no_module_access()
    {
        // Użytkownik bez subskrypcji nie powinien mieć dostępu do modułów
        $this->assertFalse($this->user->canAccessModule('invoices'));
        $this->assertFalse($this->user->canAccessModule('contractors'));
        $this->assertFalse($this->user->canAccessModule('products'));
    }

    /** @test */
    public function user_with_basic_subscription_has_access_to_basic_modules()
    {
        // Utworzenie aktywnej subskrypcji dla użytkownika
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->basicPlan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdzenie dostępu do modułów
        $this->assertTrue($this->user->canAccessModule('dashboard'));
        $this->assertTrue($this->user->canAccessModule('invoices'));
        $this->assertTrue($this->user->canAccessModule('contractors'));
        $this->assertTrue($this->user->canAccessModule('products'));
        
        // Brak dostępu do modułów wyższych planów
        $this->assertFalse($this->user->canAccessModule('finances'));
        $this->assertFalse($this->user->canAccessModule('estimates'));
        $this->assertFalse($this->user->canAccessModule('warehouse'));
    }

    /** @test */
    public function user_with_business_subscription_has_access_to_business_modules()
    {
        // Utworzenie aktywnej subskrypcji dla użytkownika
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->businessPlan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdzenie dostępu do modułów
        $this->assertTrue($this->user->canAccessModule('dashboard'));
        $this->assertTrue($this->user->canAccessModule('invoices'));
        $this->assertTrue($this->user->canAccessModule('contractors'));
        $this->assertTrue($this->user->canAccessModule('products'));
        $this->assertTrue($this->user->canAccessModule('finances'));
        $this->assertTrue($this->user->canAccessModule('estimates'));
        $this->assertTrue($this->user->canAccessModule('warehouse'));
    }

    /** @test */
    public function expired_subscription_does_not_give_module_access()
    {
        // Utworzenie wygasłej subskrypcji dla użytkownika
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->basicPlan->id,
            'status' => 'expired',
            'start_date' => Carbon::now()->subMonths(2),
            'end_date' => Carbon::now()->subMonth(),
        ]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdzenie braku dostępu do modułów
        $this->assertFalse($this->user->canAccessModule('dashboard'));
        $this->assertFalse($this->user->canAccessModule('invoices'));
        $this->assertFalse($this->user->canAccessModule('contractors'));
        $this->assertFalse($this->user->canAccessModule('products'));
    }

    /** @test */
    public function cancelled_subscription_does_not_give_module_access()
    {
        // Utworzenie anulowanej subskrypcji dla użytkownika
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->basicPlan->id,
            'status' => 'cancelled',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdzenie braku dostępu do modułów
        $this->assertFalse($this->user->canAccessModule('dashboard'));
        $this->assertFalse($this->user->canAccessModule('invoices'));
        $this->assertFalse($this->user->canAccessModule('contractors'));
        $this->assertFalse($this->user->canAccessModule('products'));
    }

    /** @test */
    public function user_subscription_respects_module_limitations()
    {
        // Utworzenie aktywnej subskrypcji dla użytkownika
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->basicPlan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Pobranie ograniczeń dla modułu
        $invoiceRestrictions = $this->user->getModuleRestrictions('invoices');
        $contractorRestrictions = $this->user->getModuleRestrictions('contractors');
        
        // Sprawdzenie wartości ograniczeń
        $this->assertNotNull($invoiceRestrictions);
        
        // Jeśli restrictions jest stringiem (np. w formacie JSON), zdekoduj go
        if (is_string($invoiceRestrictions)) {
            $invoiceRestrictions = json_decode($invoiceRestrictions, true);
        }
        
        $this->assertEquals(50, $invoiceRestrictions['max_invoices']);
        $this->assertFalse($invoiceRestrictions['export_allowed']);
        
        $this->assertNotNull($contractorRestrictions);
        
        // Jeśli restrictions jest stringiem (np. w formacie JSON), zdekoduj go
        if (is_string($contractorRestrictions)) {
            $contractorRestrictions = json_decode($contractorRestrictions, true);
        }
        
        $this->assertEquals(50, $contractorRestrictions['max_contractors']);
    }

    /** @test */
    public function user_with_direct_module_access_can_override_subscription_permissions()
    {
        // Utworzenie aktywnej subskrypcji dla użytkownika
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->basicPlan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Przyznanie bezpośredniego dostępu do modułu, który nie jest w subskrypcji
        $this->modulePermissionService->grantModuleAccess(
            $this->user,
            'finances',
            ['restrictions' => ['custom_limit' => 100]]
        );

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdzenie dostępu do modułu spoza planu
        $this->assertTrue($this->user->canAccessModule('finances'));
        
        // Sprawdzenie, czy ograniczenia zostały zastosowane
        $restrictions = $this->user->getModuleRestrictions('finances');
        
        // Jeśli restrictions jest stringiem (np. w formacie JSON), zdekoduj go
        if (is_string($restrictions)) {
            $restrictions = json_decode($restrictions, true);
        }
        
        $this->assertEquals(100, $restrictions['custom_limit']);
        
        // Nadpisanie ograniczeń modułu z subskrypcji
        $this->modulePermissionService->grantModuleAccess(
            $this->user,
            'invoices',
            ['restrictions' => ['max_invoices' => 200, 'export_allowed' => true]]
        );
        
        // Odświeżenie użytkownika
        $this->user->refresh();
        
        // Sprawdzenie, czy nowe ograniczenia zostały zastosowane
        $newRestrictions = $this->user->getModuleRestrictions('invoices');
        
        // Jeśli restrictions jest stringiem (np. w formacie JSON), zdekoduj go
        if (is_string($newRestrictions)) {
            $newRestrictions = json_decode($newRestrictions, true);
        }
        
        $this->assertEquals(200, $newRestrictions['max_invoices']);
        $this->assertTrue($newRestrictions['export_allowed']);
    }

    /** @test */
    public function changing_subscription_plan_updates_module_access()
    {
        // Utworzenie aktywnej subskrypcji dla użytkownika
        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->basicPlan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdzenie początkowego dostępu
        $this->assertTrue($this->user->canAccessModule('invoices'));
        $this->assertFalse($this->user->canAccessModule('finances'));

        // Zmiana planu subskrypcji
        $subscription->update(['subscription_plan_id' => $this->businessPlan->id]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdzenie zaktualizowanego dostępu
        $this->assertTrue($this->user->canAccessModule('invoices'));
        $this->assertTrue($this->user->canAccessModule('finances'));
        
        // Sprawdzenie, czy limity zostały zaktualizowane
        $invoiceRestrictions = $this->user->getModuleRestrictions('invoices');
        
        // Jeśli restrictions jest stringiem (np. w formacie JSON), zdekoduj go
        if (is_string($invoiceRestrictions)) {
            $invoiceRestrictions = json_decode($invoiceRestrictions, true);
        }
        
        $this->assertEquals(500, $invoiceRestrictions['max_invoices']);
        $this->assertTrue($invoiceRestrictions['export_allowed']);
    }

    /** @test */
    public function can_have_multiple_active_subscriptions_with_combined_access()
    {
        // Najpierw sprawdźmy testy bez żadnych subskrypcji
        $this->assertFalse($this->user->canAccessModule('dashboard'));
        $this->assertFalse($this->user->canAccessModule('finances'));

        // Utworzenie pierwszej aktywnej subskrypcji dla użytkownika (basic)
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->basicPlan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdźmy dostęp po pierwszej subskrypcji
        $this->assertTrue($this->user->canAccessModule('dashboard'), 'Użytkownik powinien mieć dostęp do dashboard z basic planu');
        $this->assertFalse($this->user->canAccessModule('finances'), 'Użytkownik nie powinien mieć dostępu do finances z samego basic planu');

        // Utworzenie specjalnego planu z modułem finances
        $customPlan = SubscriptionPlan::create([
            'name' => 'Custom Addon',
            'code' => 'custom-addon',
            'description' => 'Dodatkowy moduł',
            'price' => 19.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'is_active' => true,
            'display_order' => 4
        ]);

        // Przypisanie modułu finances do customPlan
        $this->modulePermissionService->assignModulesToPlan(
            $customPlan,
            ['finances'],
            ['finances' => ['max_reports' => 5]]
        );

        // Sprawdzenie czy przypisanie się powiodło
        $this->assertTrue($customPlan->hasModuleAccess('finances'), 'Plan custom powinien mieć dostęp do modułu finances');

        // Utworzenie drugiej aktywnej subskrypcji dla tego samego użytkownika
        // dodającej dostęp do modułu finanse
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $customPlan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Odświeżenie użytkownika
        $this->user->refresh();

        // Sprawdzenie łącznego dostępu z obu subskrypcji
        $this->assertTrue($this->user->canAccessModule('dashboard'));
        $this->assertTrue($this->user->canAccessModule('invoices'));
        $this->assertTrue($this->user->canAccessModule('contractors'));
        $this->assertTrue($this->user->canAccessModule('products'));
        $this->assertTrue($this->user->canAccessModule('finances'));
        
        // Brak dostępu do modułów spoza subskrypcji
        $this->assertFalse($this->user->canAccessModule('estimates'));
        $this->assertFalse($this->user->canAccessModule('warehouse'));
        
        // Sprawdzenie limitów z dodatkowego planu
        $financeRestrictions = $this->user->getModuleRestrictions('finances');
        
        // Jeśli restrictions jest stringiem (np. w formacie JSON), zdekoduj go
        if (is_string($financeRestrictions)) {
            $financeRestrictions = json_decode($financeRestrictions, true);
        }
        
        $this->assertArrayHasKey('max_reports', $financeRestrictions);
        $this->assertEquals(5, $financeRestrictions['max_reports']);
    }
} 