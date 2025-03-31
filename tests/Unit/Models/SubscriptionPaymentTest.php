<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class SubscriptionPaymentTest extends TestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    private $user;
    private $plan;
    private $subscription;
    private $payment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();

        // Tworzenie użytkownika
        $this->user = User::factory()->create();
        
        // Tworzenie planu subskrypcji
        $this->plan = SubscriptionPlan::create([
            'name' => 'Plan testowy',
            'code' => 'test-plan',
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
            'plan_id' => $this->plan->id,
            'name' => $this->plan->name,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'auto_renew' => true
        ]);
        
        // Tworzenie płatności
        $this->payment = SubscriptionPayment::create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'transaction_id' => 'TXN' . rand(100000, 999999),
            'amount' => 49.99,
            'currency' => 'PLN',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'payment_details' => 'Visa **** 4242',
            'invoice_id' => null,
            'payment_date' => Carbon::now(),
            'notes' => 'Notatka testowa'
        ]);
    }

    /**
     * Test sprawdzający poprawne tworzenie płatności
     */
    public function test_can_create_payment(): void
    {
        $this->assertInstanceOf(SubscriptionPayment::class, $this->payment);
        $this->assertEquals($this->user->id, $this->payment->user_id);
        $this->assertEquals($this->subscription->id, $this->payment->subscription_id);
        $this->assertEquals(49.99, $this->payment->amount);
        $this->assertEquals('PLN', $this->payment->currency);
        $this->assertEquals('completed', $this->payment->status);
        $this->assertEquals('credit_card', $this->payment->payment_method);
    }

    /**
     * Test sprawdzający relację z użytkownikiem
     */
    public function test_payment_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->payment->user);
        $this->assertEquals($this->user->id, $this->payment->user->id);
    }

    /**
     * Test sprawdzający relację z subskrypcją
     */
    public function test_payment_belongs_to_subscription(): void
    {
        $this->assertInstanceOf(UserSubscription::class, $this->payment->subscription);
        $this->assertEquals($this->subscription->id, $this->payment->subscription->id);
    }

    /**
     * Test sprawdzający formatowanie kwoty
     */
    public function test_formats_amount_correctly(): void
    {
        $this->assertEquals('49,99 PLN', $this->payment->formatted_amount);
        
        $this->payment->update([
            'amount' => 99.90,
            'currency' => 'EUR'
        ]);
        $this->payment->refresh();
        
        $this->assertEquals('99,90 EUR', $this->payment->formatted_amount);
    }

    /**
     * Test sprawdzający formatowanie daty płatności
     */
    public function test_formats_payment_date_correctly(): void
    {
        $date = Carbon::now()->format('d.m.Y H:i');
        $this->assertEquals($date, $this->payment->formatted_payment_date);
    }

    /**
     * Test sprawdzający metodę zwrotu płatności
     */
    public function test_refund_payment_method(): void
    {
        $this->assertEquals('completed', $this->payment->status);
        
        $this->payment->refund('Zwrot na życzenie klienta');
        $this->payment->refresh();
        
        $this->assertEquals('refunded', $this->payment->status);
        $this->assertEquals('Zwrot na życzenie klienta', $this->payment->refund_reason);
        $this->assertNotNull($this->payment->refunded_at);
    }

    /**
     * Test sprawdzający statusy płatności
     */
    public function test_payment_status_methods(): void
    {
        $this->assertTrue($this->payment->isCompleted());
        $this->assertFalse($this->payment->isPending());
        $this->assertFalse($this->payment->isFailed());
        $this->assertFalse($this->payment->isRefunded());
        
        $this->payment->update(['status' => 'pending']);
        $this->payment->refresh();
        
        $this->assertFalse($this->payment->isCompleted());
        $this->assertTrue($this->payment->isPending());
        $this->assertFalse($this->payment->isFailed());
        $this->assertFalse($this->payment->isRefunded());
        
        $this->payment->update(['status' => 'failed']);
        $this->payment->refresh();
        
        $this->assertFalse($this->payment->isCompleted());
        $this->assertFalse($this->payment->isPending());
        $this->assertTrue($this->payment->isFailed());
        $this->assertFalse($this->payment->isRefunded());
        
        $this->payment->update(['status' => 'refunded']);
        $this->payment->refresh();
        
        $this->assertFalse($this->payment->isCompleted());
        $this->assertFalse($this->payment->isPending());
        $this->assertFalse($this->payment->isFailed());
        $this->assertTrue($this->payment->isRefunded());
    }

    /**
     * Test sprawdzający generowanie faktury dla płatności
     */
    public function test_generate_invoice_method(): void
    {
        $this->assertNull($this->payment->invoice_id);
        
        $invoiceId = 'INV-' . rand(1000, 9999);
        $this->payment->generateInvoice($invoiceId);
        $this->payment->refresh();
        
        $this->assertEquals($invoiceId, $this->payment->invoice_id);
        $this->assertNotNull($this->payment->invoice_date);
    }
}
