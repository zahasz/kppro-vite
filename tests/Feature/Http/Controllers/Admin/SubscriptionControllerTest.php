<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $regularUser;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tworzenie ról
        Role::create(['name' => 'admin']);
        
        // Tworzenie użytkowników
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
        
        // Tworzenie planu subskrypcji
        $this->plan = SubscriptionPlan::create([
            'name' => 'Plan testowy',
            'code' => 'test-plan',
            'description' => 'Plan testowy dla testów',
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
     * Test sprawdzający wyświetlanie listy planów subskrypcji
     */
    public function test_index_displays_subscription_plans(): void
    {
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.index');
        $response->assertViewHas('plans');
        $response->assertSee('Plan testowy');
        $response->assertSee('49,99 PLN');
    }

    /**
     * Test sprawdzający formularz tworzenia nowego planu subskrypcji
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions/create');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.create');
    }

    /**
     * Test sprawdzający tworzenie nowego planu subskrypcji
     */
    public function test_store_creates_new_subscription_plan(): void
    {
        $planData = [
            'name' => 'Plan Premium',
            'code' => 'premium',
            'description' => 'Plan premium z dostępem do wszystkich funkcji',
            'price' => 99.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['finance', 'warehouse', 'contracts', 'estimates', 'api'],
            'max_users' => 10,
            'max_invoices' => 500,
            'max_products' => 1000,
            'max_clients' => 100,
            'is_active' => true,
            'display_order' => 2
        ];
        
        $response = $this->actingAs($this->adminUser)
                        ->post('/admin/subscriptions', $planData);
        
        $response->assertRedirect('/admin/subscriptions');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Plan Premium',
            'code' => 'premium',
            'price' => 99.99
        ]);
    }

    /**
     * Test sprawdzający formularz edycji planu subskrypcji
     */
    public function test_edit_displays_form_with_plan_data(): void
    {
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions/' . $this->plan->id . '/edit');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.create');
        $response->assertViewHas('plan');
        $response->assertSee('Plan testowy');
        $response->assertSee('49.99');
    }

    /**
     * Test sprawdzający aktualizację planu subskrypcji
     */
    public function test_update_updates_subscription_plan(): void
    {
        $updatedData = [
            'name' => 'Plan Zaktualizowany',
            'code' => 'test-plan', // pozostawiamy ten sam kod
            'description' => 'Zaktualizowany opis planu',
            'price' => 59.99,
            'currency' => 'PLN',
            'billing_period' => 'annually',
            'features' => ['finance', 'warehouse', 'contracts', 'estimates'],
            'max_users' => 8,
            'max_invoices' => 200,
            'max_products' => 800,
            'max_clients' => 80,
            'is_active' => true,
            'display_order' => 1
        ];
        
        $response = $this->actingAs($this->adminUser)
                        ->put('/admin/subscriptions/' . $this->plan->id, $updatedData);
        
        $response->assertRedirect('/admin/subscriptions');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $this->plan->id,
            'name' => 'Plan Zaktualizowany',
            'price' => 59.99,
            'billing_period' => 'annually'
        ]);
    }

    /**
     * Test sprawdzający zmianę statusu planu subskrypcji
     */
    public function test_toggle_status_changes_plan_status(): void
    {
        $this->assertTrue($this->plan->is_active);
        
        $response = $this->actingAs($this->adminUser)
                        ->post('/admin/subscriptions/' . $this->plan->id . '/toggle-status');
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->plan->refresh();
        $this->assertFalse($this->plan->is_active);
        
        // Ponowna zmiana statusu
        $response = $this->actingAs($this->adminUser)
                        ->post('/admin/subscriptions/' . $this->plan->id . '/toggle-status');
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->plan->refresh();
        $this->assertTrue($this->plan->is_active);
    }

    /**
     * Test sprawdzający wyświetlanie listy subskrypcji użytkowników
     */
    public function test_index_displays_user_subscriptions(): void
    {
        // Tworzenie subskrypcji dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->plan->name,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions/users');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.users');
        $response->assertViewHas('subscriptions');
        $response->assertSee($this->regularUser->name);
        $response->assertSee('Plan testowy');
    }

    /**
     * Test sprawdzający wyświetlanie szczegółów subskrypcji użytkownika
     */
    public function test_show_displays_subscription_details(): void
    {
        // Tworzenie subskrypcji dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->plan->name,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions/users/' . $subscription->id);
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.show');
        $response->assertViewHas('subscription');
        $response->assertSee($this->regularUser->name);
        $response->assertSee('Plan testowy');
    }

    /**
     * Test sprawdzający formularz tworzenia nowej subskrypcji
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions/users/create');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.create-user-subscription');
    }

    /**
     * Test sprawdzający tworzenie nowej subskrypcji dla użytkownika
     */
    public function test_store_user_subscription_creates_new_subscription(): void
    {
        $subscriptionData = [
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->plan->id,
            'name' => $this->plan->name,
            'status' => 'active',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonth()->format('Y-m-d'),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'notes' => 'Notatka testowa',
            'send_notification' => true,
            'auto_renew' => true
        ];
        
        $response = $this->actingAs($this->adminUser)
                        ->post('/admin/subscriptions/users', $subscriptionData);
        
        $response->assertRedirect('/admin/subscriptions/users');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->regularUser->id,
            'plan_id' => $this->plan->id,
            'name' => $this->plan->name,
            'status' => 'active',
            'payment_method' => 'credit_card'
        ]);
    }

    /**
     * Test sprawdzający formularz edycji subskrypcji użytkownika
     */
    public function test_edit_user_subscription_displays_form(): void
    {
        // Tworzenie subskrypcji dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->plan->name,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions/users/' . $subscription->id . '/edit');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.edit-user-subscription');
        $response->assertViewHas('subscription');
        $response->assertSee($this->regularUser->name);
        $response->assertSee('Plan testowy');
    }

    /**
     * Test sprawdzający aktualizację subskrypcji użytkownika
     */
    public function test_update_user_subscription_updates_subscription(): void
    {
        // Tworzenie subskrypcji dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->plan->name,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true
        ]);
        
        $updatedData = [
            'plan_id' => $this->plan->id,
            'name' => 'Zaktualizowany plan',
            'status' => 'trial',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
            'payment_method' => 'paypal',
            'payment_details' => 'paypal@example.com',
            'notes' => 'Zaktualizowana notatka',
            'auto_renew' => false
        ];
        
        $response = $this->actingAs($this->adminUser)
                        ->put('/admin/subscriptions/users/' . $subscription->id, $updatedData);
        
        $response->assertRedirect('/admin/subscriptions/users');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('user_subscriptions', [
            'id' => $subscription->id,
            'name' => 'Zaktualizowany plan',
            'status' => 'trial',
            'payment_method' => 'paypal',
            'auto_renew' => false
        ]);
    }

    /**
     * Test sprawdzający anulowanie subskrypcji użytkownika
     */
    public function test_cancel_user_subscription_cancels_subscription(): void
    {
        // Tworzenie subskrypcji dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->plan->name,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->post('/admin/subscriptions/users/' . $subscription->id . '/cancel');
        
        $response->assertRedirect('/admin/subscriptions/users');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('user_subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled',
            'auto_renew' => false
        ]);
    }

    /**
     * Test sprawdzający wyświetlanie historii płatności
     */
    public function test_payments_displays_payment_history(): void
    {
        // Tworzenie subskrypcji dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->plan->name,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true
        ]);
        
        // Tworzenie płatności dla testów
        $payment = SubscriptionPayment::create([
            'user_id' => $this->regularUser->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => 'TXN' . rand(100000, 999999),
            'amount' => 49.99,
            'currency' => 'PLN',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'payment_date' => Carbon::now()
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions/payments');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.payments');
        $response->assertViewHas('payments');
        $response->assertSee($payment->transaction_id);
        $response->assertSee('49,99 PLN');
    }

    /**
     * Test sprawdzający wyświetlanie szczegółów płatności
     */
    public function test_payment_details_displays_payment_info(): void
    {
        // Tworzenie subskrypcji dla testów
        $subscription = UserSubscription::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->plan->name,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true
        ]);
        
        // Tworzenie płatności dla testów
        $payment = SubscriptionPayment::create([
            'user_id' => $this->regularUser->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => 'TXN' . rand(100000, 999999),
            'amount' => 49.99,
            'currency' => 'PLN',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'payment_date' => Carbon::now()
        ]);
        
        $response = $this->actingAs($this->adminUser)
                        ->get('/admin/subscriptions/payments/' . $payment->id);
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.payment-details');
        $response->assertViewHas('payment');
        $response->assertSee($payment->transaction_id);
        $response->assertSee($this->regularUser->name);
        $response->assertSee('Plan testowy');
    }

    /**
     * Test sprawdzający przeszukiwanie użytkowników poprzez API
     */
    public function test_search_users_returns_matching_users(): void
    {
        $response = $this->actingAs($this->adminUser)
                        ->getJson('/admin/subscriptions/users/search?query=' . $this->regularUser->email);
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['users']);
        
        $responseData = $response->json();
        $this->assertGreaterThan(0, count($responseData['users']));
        $this->assertEquals($this->regularUser->id, $responseData['users'][0]['id']);
    }
}
