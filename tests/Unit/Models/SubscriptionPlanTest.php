<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionPlanTest extends TestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    private $plan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();

        // Tworzenie przykładowego planu subskrypcji
        $this->plan = SubscriptionPlan::create([
            'name' => 'Plan testowy',
            'code' => 'test-plan',
            'description' => 'Opis planu testowego',
            'price' => 49.99,
            'currency' => 'PLN',
            'billing_period' => 'monthly',
            'features' => ['finance', 'warehouse', 'contracts'],
            'max_users' => 5,
            'max_invoices' => 100,
            'max_products' => 500,
            'max_clients' => 50,
            'is_active' => true,
            'display_order' => 1
        ]);
    }

    /**
     * Test sprawdzający poprawne tworzenie planu subskrypcji
     */
    public function test_can_create_subscription_plan(): void
    {
        $this->assertInstanceOf(SubscriptionPlan::class, $this->plan);
        $this->assertEquals('Plan testowy', $this->plan->name);
        $this->assertEquals('test-plan', $this->plan->code);
        $this->assertEquals(49.99, $this->plan->price);
        $this->assertEquals('monthly', $this->plan->billing_period);
        $this->assertTrue($this->plan->is_active);
    }

    /**
     * Test sprawdzający relację z subskrypcjami użytkowników
     */
    public function test_plan_has_many_user_subscriptions(): void
    {
        $this->assertInstanceOf(Collection::class, $this->plan->userSubscriptions);
    }

    /**
     * Test sprawdzający formatowanie ceny
     */
    public function test_formats_price_correctly(): void
    {
        $this->assertEquals('49,99 PLN', $this->plan->formatted_price);
        
        $this->plan->update(['price' => 99.90]);
        $this->plan->refresh();
        
        $this->assertEquals('99,90 PLN', $this->plan->formatted_price);
    }

    /**
     * Test sprawdzający formatowanie okresu rozliczeniowego
     */
    public function test_formats_billing_period_correctly(): void
    {
        $this->assertEquals('Miesięczny', $this->plan->formatted_billing_period);
        
        $this->plan->update(['billing_period' => 'annually']);
        $this->plan->refresh();
        
        $this->assertEquals('Roczny', $this->plan->formatted_billing_period);
        
        $this->plan->update(['billing_period' => 'quarterly']);
        $this->plan->refresh();
        
        $this->assertEquals('Kwartalny', $this->plan->formatted_billing_period);
        
        $this->plan->update(['billing_period' => 'lifetime']);
        $this->plan->refresh();
        
        $this->assertEquals('Bezterminowy', $this->plan->formatted_billing_period);
    }

    /**
     * Test sprawdzający, czy plan zawiera określoną funkcję
     */
    public function test_has_feature_method(): void
    {
        $this->assertTrue($this->plan->hasFeature('finance'));
        $this->assertTrue($this->plan->hasFeature('warehouse'));
        $this->assertTrue($this->plan->hasFeature('contracts'));
        $this->assertFalse($this->plan->hasFeature('api'));
    }

    /**
     * Test sprawdzający walidację planu subskrypcji
     */
    public function test_subscription_plan_validations(): void
    {
        $plan = new SubscriptionPlan();
        
        // Plan powinien wymagać nazwy
        $this->expectException(\Illuminate\Database\QueryException::class);
        $plan->save();
    }

    /**
     * Test sprawdzający liczbę użytkowników na planie
     */
    public function test_counts_users_correctly(): void
    {
        $this->assertEquals(0, $this->plan->users_count);
        
        // Dodanie użytkowników
        UserSubscription::create([
            'user_id' => 1,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'payment_method' => 'credit_card'
        ]);
        
        UserSubscription::create([
            'user_id' => 2,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'payment_method' => 'paypal'
        ]);
        
        $this->plan->refresh();
        $this->assertEquals(2, $this->plan->users_count);
    }
}
