<?php

namespace Tests\Feature\Http\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;

class CheckSubscriptionFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $regularUser;
    protected $planWithWarehouse;
    protected $planWithoutWarehouse;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tworzenie użytkownika
        $this->regularUser = User::factory()->create();
        
        // Tworzenie planów subskrypcji
        $this->planWithWarehouse = SubscriptionPlan::create([
            'name' => 'Plan z magazynem',
            'code' => 'plan-with-warehouse',
            'description' => 'Plan z dostępem do magazynu',
            'price' => 49.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['finance', 'warehouse'],
            'max_users' => 5,
            'max_invoices' => 100,
            'max_products' => 500,
            'max_clients' => 50,
            'is_active' => true,
            'display_order' => 1
        ]);
        
        $this->planWithoutWarehouse = SubscriptionPlan::create([
            'name' => 'Plan bez magazynu',
            'code' => 'plan-without-warehouse',
            'description' => 'Plan bez dostępu do magazynu',
            'price' => 29.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['finance'],
            'max_users' => 2,
            'max_invoices' => 50,
            'max_products' => 100,
            'max_clients' => 20,
            'is_active' => true,
            'display_order' => 2
        ]);
        
        // Definicja testowej trasy z middleware
        Route::get('/test-warehouse', function () {
            return response('Dostęp do magazynu', 200);
        })->middleware(['auth', 'subscription.feature:warehouse'])->name('test.warehouse');
        
        Route::get('/test-finance', function () {
            return response('Dostęp do finansów', 200);
        })->middleware(['auth', 'subscription.feature:finance'])->name('test.finance');
    }

    /**
     * Test sprawdzający, czy użytkownik z planem zawierającym funkcję ma dostęp
     */
    public function test_user_with_feature_can_access(): void
    {
        // Przypisanie użytkownikowi planu z funkcją magazynu
        UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->planWithWarehouse->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->regularUser)
                        ->get('/test-warehouse');
        
        $response->assertStatus(200);
        $response->assertSee('Dostęp do magazynu');
    }

    /**
     * Test sprawdzający, czy użytkownik bez planu zawierającego funkcję nie ma dostępu
     */
    public function test_user_without_feature_cannot_access(): void
    {
        // Przypisanie użytkownikowi planu bez funkcji magazynu
        UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->planWithoutWarehouse->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->regularUser)
                        ->get('/test-warehouse');
        
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Twój plan subskrypcji nie obejmuje dostępu do tej funkcji. Zaktualizuj swój plan, aby uzyskać dostęp.');
    }

    /**
     * Test sprawdzający, czy użytkownik z planem zawierającym funkcję, ale nieaktywnym, nie ma dostępu
     */
    public function test_user_with_inactive_subscription_cannot_access(): void
    {
        // Przypisanie użytkownikowi planu z funkcją magazynu, ale nieaktywnego
        UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->planWithWarehouse->id,
            'status' => 'inactive',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->regularUser)
                        ->get('/test-warehouse');
        
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Twoja subskrypcja jest nieaktywna. Odnów subskrypcję, aby uzyskać dostęp.');
    }

    /**
     * Test sprawdzający, czy użytkownik z planem zawierającym funkcję, ale wygasłym, nie ma dostępu
     */
    public function test_user_with_expired_subscription_cannot_access(): void
    {
        // Przypisanie użytkownikowi planu z funkcją magazynu, ale z wygasłą datą
        UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->planWithWarehouse->id,
            'status' => 'active',
            'start_date' => Carbon::now()->subMonths(2),
            'end_date' => Carbon::now()->subMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->regularUser)
                        ->get('/test-warehouse');
        
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Twoja subskrypcja wygasła. Odnów subskrypcję, aby uzyskać dostęp.');
    }

    /**
     * Test sprawdzający, czy użytkownik bez subskrypcji nie ma dostępu
     */
    public function test_user_without_subscription_cannot_access(): void
    {
        $response = $this->actingAs($this->regularUser)
                        ->get('/test-warehouse');
        
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Nie masz aktywnej subskrypcji. Wybierz plan, aby uzyskać dostęp.');
    }

    /**
     * Test sprawdzający, czy API zwraca błąd 403 dla użytkownika bez funkcji
     */
    public function test_api_returns_error_for_user_without_feature(): void
    {
        // Przypisanie użytkownikowi planu bez funkcji magazynu
        UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->planWithoutWarehouse->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->regularUser)
                        ->getJson('/test-warehouse');
        
        $response->assertStatus(403);
        $response->assertJson(['error' => 'Brak dostępu do tej funkcji w Twoim planie subskrypcji.']);
    }

    /**
     * Test sprawdzający, czy użytkownik ma dostęp do wszystkich funkcji w jego planie
     */
    public function test_user_can_access_all_features_in_plan(): void
    {
        // Przypisanie użytkownikowi planu z funkcją magazynu i finansów
        UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->planWithWarehouse->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        // Test dostępu do magazynu
        $responseWarehouse = $this->actingAs($this->regularUser)
                                ->get('/test-warehouse');
        
        $responseWarehouse->assertStatus(200);
        $responseWarehouse->assertSee('Dostęp do magazynu');
        
        // Test dostępu do finansów
        $responseFinance = $this->actingAs($this->regularUser)
                            ->get('/test-finance');
        
        $responseFinance->assertStatus(200);
        $responseFinance->assertSee('Dostęp do finansów');
    }
}
