<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Models\PaymentGateway;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTransactionTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $gateway;
    private $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->gateway = PaymentGateway::factory()->create();
        $this->subscription = UserSubscription::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_create_payment_transaction()
    {
        $transaction = PaymentTransaction::create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'gateway_code' => $this->gateway->code,
            'transaction_id' => 'test_transaction_123',
            'amount' => 100.00,
            'currency' => 'PLN',
            'status' => 'pending',
            'payment_method' => 'card'
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'transaction_id' => 'test_transaction_123',
            'amount' => 100.00,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function it_can_update_transaction_status()
    {
        $transaction = PaymentTransaction::create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'gateway_code' => $this->gateway->code,
            'transaction_id' => 'test_transaction_123',
            'amount' => 100.00,
            'currency' => 'PLN',
            'status' => 'pending'
        ]);

        $transaction->status = 'completed';
        $transaction->save();

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $transaction->id,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function it_can_add_error_message()
    {
        $transaction = PaymentTransaction::create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'gateway_code' => $this->gateway->code,
            'transaction_id' => 'test_transaction_123',
            'amount' => 100.00,
            'currency' => 'PLN',
            'status' => 'failed'
        ]);

        $transaction->error_message = 'Payment failed due to insufficient funds';
        $transaction->save();

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $transaction->id,
            'error_message' => 'Payment failed due to insufficient funds'
        ]);
    }

    /** @test */
    public function it_can_store_gateway_response()
    {
        $transaction = PaymentTransaction::create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'gateway_code' => $this->gateway->code,
            'transaction_id' => 'test_transaction_123',
            'amount' => 100.00,
            'currency' => 'PLN',
            'status' => 'completed'
        ]);

        $response = [
            'status' => 'success',
            'transaction_id' => 'test_transaction_123',
            'timestamp' => now()->toIso8601String()
        ];

        $transaction->gateway_response = $response;
        $transaction->save();

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $transaction->id,
            'gateway_response->status' => 'success'
        ]);
    }

    /** @test */
    public function it_can_store_metadata()
    {
        $transaction = PaymentTransaction::create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'gateway_code' => $this->gateway->code,
            'transaction_id' => 'test_transaction_123',
            'amount' => 100.00,
            'currency' => 'PLN',
            'status' => 'completed'
        ]);

        $metadata = [
            'payment_details' => [
                'card_last4' => '4242',
                'card_brand' => 'visa'
            ],
            'customer_ip' => '127.0.0.1'
        ];

        $transaction->metadata = $metadata;
        $transaction->save();

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $transaction->id,
            'metadata->payment_details->card_last4' => '4242'
        ]);
    }
} 