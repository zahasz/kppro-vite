<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_payment_gateway()
    {
        $gateway = PaymentGateway::create([
            'name' => 'Test Gateway',
            'code' => 'test_gateway',
            'class_name' => 'App\Gateways\TestGateway',
            'description' => 'Test payment gateway',
            'is_active' => true,
            'test_mode' => true,
            'display_order' => 1,
            'config' => ['api_key' => 'test_key']
        ]);

        $this->assertDatabaseHas('payment_gateways', [
            'name' => 'Test Gateway',
            'code' => 'test_gateway',
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_can_update_payment_gateway()
    {
        $gateway = PaymentGateway::create([
            'name' => 'Test Gateway',
            'code' => 'test_gateway',
            'class_name' => 'App\Gateways\TestGateway',
            'is_active' => true
        ]);

        $gateway->update([
            'name' => 'Updated Gateway',
            'is_active' => false
        ]);

        $this->assertDatabaseHas('payment_gateways', [
            'id' => $gateway->id,
            'name' => 'Updated Gateway',
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_can_delete_payment_gateway()
    {
        $gateway = PaymentGateway::create([
            'name' => 'Test Gateway',
            'code' => 'test_gateway',
            'class_name' => 'App\Gateways\TestGateway',
            'is_active' => true
        ]);

        $gateway->delete();

        $this->assertDatabaseMissing('payment_gateways', [
            'id' => $gateway->id
        ]);
    }

    /** @test */
    public function it_can_toggle_gateway_status()
    {
        $gateway = PaymentGateway::create([
            'name' => 'Test Gateway',
            'code' => 'test_gateway',
            'class_name' => 'App\Gateways\TestGateway',
            'is_active' => true
        ]);

        $gateway->is_active = false;
        $gateway->save();

        $this->assertDatabaseHas('payment_gateways', [
            'id' => $gateway->id,
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_can_toggle_test_mode()
    {
        $gateway = PaymentGateway::create([
            'name' => 'Test Gateway',
            'code' => 'test_gateway',
            'class_name' => 'App\Gateways\TestGateway',
            'test_mode' => true
        ]);

        $gateway->test_mode = false;
        $gateway->save();

        $this->assertDatabaseHas('payment_gateways', [
            'id' => $gateway->id,
            'test_mode' => false
        ]);
    }
} 