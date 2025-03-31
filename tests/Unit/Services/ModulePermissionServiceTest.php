<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Module;
use App\Models\SubscriptionPlan;
use App\Models\UserModulePermission;
use App\Services\ModulePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ModulePermissionServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $modulePermissionService;
    protected $user;
    protected $module;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        // Inicjalizacja serwisu
        $this->modulePermissionService = app(ModulePermissionService::class);

        // Tworzenie użytkownika testowego
        $this->user = User::factory()->create();

        // Tworzenie modułów testowych
        $this->module = Module::create([
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

        // Tworzenie planu subskrypcji
        $this->plan = SubscriptionPlan::create([
            'name' => 'Plan testowy',
            'code' => 'test-plan',
            'description' => 'Opis planu testowego',
            'price' => 49.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['finance', 'contracts'],
            'max_users' => 5,
            'max_invoices' => 100,
            'max_products' => 500,
            'max_clients' => 50,
            'is_active' => true,
            'display_order' => 1
        ]);
    }

    /** @test */
    public function it_can_assign_modules_to_subscription_plan()
    {
        // Zdefiniowanie modułów do przypisania
        $modules = ['invoices', 'contractors'];
        
        // Zdefiniowanie limitów dla modułów
        $limitations = [
            'invoices' => ['max_invoices' => 50, 'export_allowed' => false],
            'contractors' => ['max_contractors' => 50]
        ];
        
        // Przypisanie modułów do planu
        $result = $this->modulePermissionService->assignModulesToPlan($this->plan, $modules, $limitations);
        
        // Sprawdzenie, czy operacja się powiodła
        $this->assertTrue($result);
        
        // Sprawdzenie, czy moduły zostały prawidłowo przypisane
        $this->assertEquals(2, $this->plan->modules()->count());
        
        // Sprawdzenie, czy limity zostały prawidłowo zapisane
        $invoiceModule = $this->plan->modules()->where('code', 'invoices')->first();
        $this->assertNotNull($invoiceModule);
        $limitations = json_decode($invoiceModule->pivot->limitations, true);
        $this->assertEquals(50, $limitations['max_invoices']);
        $this->assertFalse($limitations['export_allowed']);
    }

    /** @test */
    public function it_can_check_if_plan_has_module_access()
    {
        // Przypisanie modułów do planu
        $this->modulePermissionService->assignModulesToPlan(
            $this->plan, 
            ['invoices'], 
            ['invoices' => ['max_invoices' => 50]]
        );
        
        // Sprawdzenie dostępu do przypisanego modułu
        $this->assertTrue($this->plan->hasModuleAccess('invoices'));
        
        // Sprawdzenie braku dostępu do nieprzypisanego modułu
        $this->assertFalse($this->plan->hasModuleAccess('products'));
    }

    /** @test */
    public function it_can_get_module_limitations_for_plan()
    {
        // Przypisanie modułów z limitami do planu
        $this->modulePermissionService->assignModulesToPlan(
            $this->plan, 
            ['invoices'], 
            ['invoices' => ['max_invoices' => 75, 'export_allowed' => true]]
        );
        
        // Pobranie limitów dla modułu
        $limitations = $this->plan->getModuleLimitations('invoices');
        
        // Sprawdzenie czy limity są poprawne
        $this->assertNotNull($limitations);
        $limitations = json_decode($limitations, true);
        $this->assertEquals(75, $limitations['max_invoices']);
        $this->assertTrue($limitations['export_allowed']);
        
        // Sprawdzenie dla nieprzypisanego modułu
        $this->assertNull($this->plan->getModuleLimitations('products'));
    }

    /** @test */
    public function it_handles_edge_cases_and_invalid_inputs()
    {
        // Test z nieistniejącym planem
        $result = $this->modulePermissionService->assignModulesToPlan(999, ['invoices'], []);
        $this->assertFalse($result);
        
        // Test z pustą listą modułów
        $result = $this->modulePermissionService->assignModulesToPlan($this->plan, [], []);
        $this->assertTrue($result);
        $this->assertEquals(0, $this->plan->modules()->count());
        
        // Test z nieistniejącymi modułami
        $result = $this->modulePermissionService->assignModulesToPlan($this->plan, ['non_existent_module'], []);
        $this->assertTrue($result);
        $this->assertEquals(0, $this->plan->modules()->count());
    }

    /** @test */
    public function it_can_grant_and_check_module_access_for_user()
    {
        // Przyznanie bezpośredniego dostępu do modułu
        $result = $this->modulePermissionService->grantModuleAccess(
            $this->user,
            'invoices',
            ['restrictions' => ['max_invoices' => 20]]
        );
        
        // Sprawdzenie rezultatu operacji
        $this->assertTrue($result);
        
        // Sprawdzenie, czy użytkownik ma dostęp do modułu
        $hasAccess = $this->modulePermissionService->userCanAccessModule($this->user, 'invoices');
        $this->assertTrue($hasAccess);
        
        // Sprawdzenie ograniczeń
        $restrictions = $this->modulePermissionService->getModuleRestrictions($this->user, 'invoices');
        $this->assertNotNull($restrictions);
        
        // Jeśli restrictions jest stringiem (np. w formacie JSON), zdekoduj go
        if (is_string($restrictions)) {
            $restrictions = json_decode($restrictions, true);
        }
        
        $this->assertArrayHasKey('max_invoices', $restrictions);
        $this->assertEquals(20, $restrictions['max_invoices']);
    }

    /** @test */
    public function it_can_deny_module_access_for_user()
    {
        // Najpierw przyznaj dostęp
        $this->modulePermissionService->grantModuleAccess($this->user, 'invoices');
        
        // Sprawdź, czy użytkownik ma dostęp
        $this->assertTrue($this->modulePermissionService->userCanAccessModule($this->user, 'invoices'));
        
        // Teraz odbierz dostęp
        $result = $this->modulePermissionService->denyModuleAccess($this->user, 'invoices', 'admin');
        
        // Sprawdź rezultat operacji
        $this->assertTrue($result);
        
        // Sprawdź, czy użytkownik nie ma już dostępu
        $this->assertFalse($this->modulePermissionService->userCanAccessModule($this->user, 'invoices'));
    }

    /** @test */
    public function it_returns_modules_with_access_info_for_user()
    {
        // Przyznanie dostępu do jednego modułu
        $this->modulePermissionService->grantModuleAccess($this->user, 'invoices');
        
        // Pobranie wszystkich modułów z informacją o dostępie
        $modules = $this->modulePermissionService->getUserModulesWithAccess($this->user);
        
        // Sprawdzenie, czy wszystkie aktywne moduły zostały zwrócone
        $this->assertEquals(3, $modules->count());
        
        // Sprawdzenie, czy status dostępu jest prawidłowy
        $invoicesModule = $modules->firstWhere('code', 'invoices');
        $contractorsModule = $modules->firstWhere('code', 'contractors');
        
        $this->assertTrue($invoicesModule->has_access);
        $this->assertFalse($contractorsModule->has_access);
        
        // Sprawdzenie typu dostępu
        $this->assertEquals('direct', $invoicesModule->access_type);
    }
} 