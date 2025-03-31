<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UserSubscriptionTest extends TestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    private $user;
    private $plan;
    private $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();

        // Tworzenie użytkownika
        $this->user = User::factory()->create();
        
        // Tworzenie planu subskrypcji z unikalnym kodem
        $this->plan = SubscriptionPlan::create([
            'name' => 'Plan testowy',
            'code' => 'test-plan-' . uniqid(),
            'description' => 'Opis planu testowego',
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
        
        // Tworzenie subskrypcji
        $this->subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'name' => $this->plan->name,
            'description' => $this->plan->description,
            'price' => $this->plan->price,
            'currency' => $this->plan->currency,
            'status' => 'active',
            'billing_period' => $this->plan->billing_period,
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true,
            'notes' => 'Notatka testowa',
            'trial_ends_at' => null,
            'ends_at' => Carbon::now()->addMonth()
        ]);
    }

    /**
     * Test sprawdzający poprawne tworzenie subskrypcji użytkownika
     */
    public function test_can_create_user_subscription(): void
    {
        $this->assertInstanceOf(UserSubscription::class, $this->subscription);
        $this->assertEquals($this->user->id, $this->subscription->user_id);
        $this->assertEquals($this->plan->id, $this->subscription->plan_id);
        $this->assertEquals('active', $this->subscription->status);
        $this->assertEquals('credit_card', $this->subscription->payment_method);
        $this->assertTrue($this->subscription->auto_renew);
    }

    /**
     * Test sprawdzający relację z użytkownikiem
     */
    public function test_subscription_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->subscription->user);
        $this->assertEquals($this->user->id, $this->subscription->user->id);
    }

    /**
     * Test sprawdzający relację z planem subskrypcji
     */
    public function test_subscription_belongs_to_plan(): void
    {
        $this->assertInstanceOf(SubscriptionPlan::class, $this->subscription->plan);
        $this->assertEquals($this->plan->id, $this->subscription->plan->id);
    }

    /**
     * Test sprawdzający relację z płatnościami
     */
    public function test_subscription_has_many_payments(): void
    {
        $this->assertInstanceOf(Collection::class, $this->subscription->payments);
    }

    /**
     * Test sprawdzający metodę sprawdzającą aktywność subskrypcji
     */
    public function test_is_active_method(): void
    {
        $this->assertTrue($this->subscription->isActive());
        
        $this->subscription->update(['status' => 'inactive']);
        $this->subscription->refresh();
        
        $this->assertFalse($this->subscription->isActive());
        
        $this->subscription->update(['status' => 'active', 'end_date' => Carbon::now()->subDay()]);
        $this->subscription->refresh();
        
        $this->assertFalse($this->subscription->isActive());
    }

    /**
     * Test sprawdzający metodę sprawdzającą, czy subskrypcja jest w okresie próbnym
     */
    public function test_is_trial_method(): void
    {
        $this->assertFalse($this->subscription->isTrial());
        
        $this->subscription->update(['status' => 'trial']);
        $this->subscription->refresh();
        
        $this->assertTrue($this->subscription->isTrial());
    }

    /**
     * Test sprawdzający formatowanie daty rozpoczęcia
     */
    public function test_formats_start_date_correctly(): void
    {
        $date = Carbon::now()->format('d.m.Y');
        $this->assertEquals($date, $this->subscription->formatted_start_date);
    }

    /**
     * Test sprawdzający formatowanie daty zakończenia
     */
    public function test_formats_end_date_correctly(): void
    {
        $date = Carbon::now()->addMonth()->format('d.m.Y');
        $this->assertEquals($date, $this->subscription->formatted_end_date);
        
        $this->subscription->update(['end_date' => null]);
        $this->subscription->refresh();
        
        $this->assertEquals('Bezterminowo', $this->subscription->formatted_end_date);
    }

    /**
     * Test sprawdzający metodę anulowania subskrypcji
     */
    public function test_cancel_subscription_method(): void
    {
        $this->assertTrue($this->subscription->auto_renew);
        
        $this->subscription->cancel();
        $this->subscription->refresh();
        
        $this->assertFalse($this->subscription->auto_renew);
        $this->assertEquals('cancelled', $this->subscription->status);
    }

    /**
     * Test sprawdzający automatyczne przedłużenie subskrypcji
     */
    public function test_renew_subscription_method(): void
    {
        $oldEndDate = $this->subscription->end_date;
        
        $this->subscription->renew();
        $this->subscription->refresh();
        
        $this->assertGreaterThan($oldEndDate, $this->subscription->end_date);
        $this->assertEquals('active', $this->subscription->status);
        
        // Sprawdzenie, czy płatność została utworzona
        $this->assertCount(1, $this->subscription->payments);
    }
}
