<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $regularUser;
    protected $plan;

    /**
     * Przygotowanie testów
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Tworzenie ról
        Role::create(['name' => 'admin']);
        
        // Tworzenie użytkowników
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
        
        $this->regularUser = User::factory()->create();
        
        // Tworzenie planu subskrypcji
        $this->plan = SubscriptionPlan::create([
            'name' => 'Test Plan',
            'code' => 'test-plan',
            'description' => 'Plan testowy',
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
    }

    /**
     * Test sprawdzający, czy admin może wyświetlić listę planów subskrypcji
     */
    public function test_admin_can_view_subscription_plans_list(): void
    {
        $response = $this->actingAs($this->adminUser)
                        ->get(route('admin.subscriptions.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.index');
        $response->assertViewHas('plans');
    }

    /**
     * Test sprawdzający, czy admin może utworzyć nowy plan subskrypcji
     */
    public function test_admin_can_create_subscription_plan(): void
    {
        $planData = [
            'name' => 'Nowy Plan',
            'code' => 'nowy-plan',
            'description' => 'Opis nowego planu',
            'price' => 99.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['finance', 'warehouse', 'estimates'],
            'max_users' => 10,
            'max_invoices' => 200,
            'max_products' => 1000,
            'max_clients' => 100,
            'is_active' => true,
            'display_order' => 2
        ];
        
        $response = $this->actingAs($this->adminUser)
                        ->post(route('admin.subscriptions.store'), $planData);
        
        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Nowy Plan',
            'code' => 'nowy-plan'
        ]);
    }

    /**
     * Test sprawdzający, czy admin może edytować istniejący plan subskrypcji
     */
    public function test_admin_can_update_subscription_plan(): void
    {
        $updatedData = [
            'name' => 'Zaktualizowany Plan',
            'code' => 'test-plan', // nie zmieniamy, żeby uniknąć problemów z unique
            'description' => 'Zaktualizowany opis planu',
            'price' => 59.99,
            'currency' => 'PLN',
            'billing_period' => 'annually',
            'features' => ['finance', 'warehouse', 'estimates', 'contracts'],
            'max_users' => 15,
            'is_active' => true,
            'display_order' => 1
        ];
        
        $response = $this->actingAs($this->adminUser)
                        ->put(route('admin.subscriptions.update', $this->plan->id), $updatedData);
        
        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $this->plan->id,
            'name' => 'Zaktualizowany Plan',
            'price' => 59.99,
            'billing_period' => 'annually'
        ]);
    }

    /**
     * Test sprawdzający, czy admin może wyświetlić listę subskrypcji użytkowników
     */
    public function test_admin_can_view_user_subscriptions(): void
    {
        // Tworzymy subskrypcję dla testów
        UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->get(route('admin.subscriptions.users'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.users');
        $response->assertViewHas('subscriptions');
    }

    /**
     * Test sprawdzający, czy admin może utworzyć nową subskrypcję dla użytkownika
     */
    public function test_admin_can_create_user_subscription(): void
    {
        $subscriptionData = [
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonth()->format('Y-m-d'),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'notes' => 'Notatka testowa',
            'send_notification' => true
        ];
        
        $response = $this->actingAs($this->adminUser)
                        ->post(route('admin.subscriptions.store-user-subscription'), $subscriptionData);
        
        $response->assertRedirect(route('admin.subscriptions.users'));
        
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->plan->id,
            'status' => 'active'
        ]);
    }

    /**
     * Test sprawdzający, czy admin może edytować istniejącą subskrypcję użytkownika
     */
    public function test_admin_can_update_user_subscription(): void
    {
        // Tworzymy subskrypcję dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        $updatedData = [
            'plan_id' => $this->plan->id,
            'status' => 'trial',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
            'payment_method' => 'paypal',
            'payment_details' => 'paypal@example.com',
            'notes' => 'Zaktualizowana notatka',
            'auto_renew' => false
        ];
        
        $response = $this->actingAs($this->adminUser)
                        ->put(route('admin.subscriptions.update-user-subscription', $subscription->id), $updatedData);
        
        $response->assertRedirect(route('admin.subscriptions.users'));
        
        $this->assertDatabaseHas('user_subscriptions', [
            'id' => $subscription->id,
            'status' => 'trial',
            'payment_method' => 'paypal',
            'auto_renew' => false
        ]);
    }

    /**
     * Test sprawdzający, czy admin może wyświetlić historię płatności
     */
    public function test_admin_can_view_payment_history(): void
    {
        // Tworzymy subskrypcję dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        // Tworzymy płatność dla testów
        SubscriptionPayment::create([
            'user_id' => $this->regularUser->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => 'TXN' . rand(100000, 999999),
            'amount' => 49.99,
            'currency' => 'PLN',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->get(route('admin.subscriptions.payments'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.payments');
        $response->assertViewHas('payments');
    }

    /**
     * Test sprawdzający, czy admin może wyświetlić szczegóły płatności
     */
    public function test_admin_can_view_payment_details(): void
    {
        // Tworzymy subskrypcję dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'auto_renew' => true
        ]);
        
        // Tworzymy płatność dla testów
        $payment = SubscriptionPayment::create([
            'user_id' => $this->regularUser->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => 'TXN' . rand(100000, 999999),
            'amount' => 49.99,
            'currency' => 'PLN',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->get(route('admin.subscriptions.payment-details', $payment->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.payment-details');
        $response->assertViewHas('payment');
    }

    /**
     * Test sprawdzający, czy regularny użytkownik nie ma dostępu do panelu subskrypcji
     */
    public function test_regular_user_cannot_access_subscription_panel(): void
    {
        $response = $this->actingAs($this->regularUser)
                        ->get(route('admin.subscriptions.index'));
        
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }
}
