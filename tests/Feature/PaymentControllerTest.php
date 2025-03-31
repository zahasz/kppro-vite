<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->gateway = PaymentGateway::factory()->create();
    }

    /** @test */
    public function admin_can_view_payment_gateways_list()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.payments.index')
            ->assertViewHas('gateways');
    }

    /** @test */
    public function admin_can_create_payment_gateway()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), [
                'name' => 'New Gateway',
                'code' => 'new_gateway',
                'class_name' => 'App\Gateways\NewGateway',
                'description' => 'New payment gateway',
                'is_active' => true,
                'test_mode' => true,
                'display_order' => 1,
                'logo' => UploadedFile::fake()->image('logo.png'),
                'config' => ['api_key' => 'test_key']
            ]);

        $response->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payment_gateways', [
            'name' => 'New Gateway',
            'code' => 'new_gateway'
        ]);
    }

    /** @test */
    public function admin_can_update_payment_gateway()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)
            ->put(route('admin.payments.update', $this->gateway), [
                'name' => 'Updated Gateway',
                'code' => $this->gateway->code,
                'class_name' => $this->gateway->class_name,
                'description' => 'Updated description',
                'is_active' => false,
                'test_mode' => true,
                'display_order' => 2,
                'logo' => UploadedFile::fake()->image('new_logo.png')
            ]);

        $response->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payment_gateways', [
            'id' => $this->gateway->id,
            'name' => 'Updated Gateway',
            'is_active' => false
        ]);
    }

    /** @test */
    public function admin_can_delete_payment_gateway()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.payments.destroy', $this->gateway));

        $response->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('payment_gateways', [
            'id' => $this->gateway->id
        ]);
    }

    /** @test */
    public function admin_cannot_delete_gateway_with_transactions()
    {
        PaymentTransaction::factory()->create([
            'gateway_code' => $this->gateway->code
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.payments.destroy', $this->gateway));

        $response->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('payment_gateways', [
            'id' => $this->gateway->id
        ]);
    }

    /** @test */
    public function admin_can_toggle_gateway_status()
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.payments.toggle-status', $this->gateway));

        $response->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payment_gateways', [
            'id' => $this->gateway->id,
            'is_active' => !$this->gateway->is_active
        ]);
    }

    /** @test */
    public function admin_can_view_transactions_list()
    {
        $transaction = PaymentTransaction::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.transactions'));

        $response->assertStatus(200)
            ->assertViewIs('admin.payments.transactions')
            ->assertViewHas('transactions')
            ->assertViewHas('gateways')
            ->assertViewHas('statusOptions');
    }

    /** @test */
    public function admin_can_view_transaction_details()
    {
        $transaction = PaymentTransaction::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.transaction-details', $transaction));

        $response->assertStatus(200)
            ->assertViewIs('admin.payments.transaction-details')
            ->assertViewHas('transaction');
    }

    /** @test */
    public function admin_can_update_transaction_status()
    {
        $transaction = PaymentTransaction::factory()->create([
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.payments.update-status', $transaction), [
                'status' => 'completed',
                'notes' => 'Payment confirmed'
            ]);

        $response->assertRedirect(route('admin.payments.transactions'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $transaction->id,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function admin_can_refund_payment()
    {
        $transaction = PaymentTransaction::factory()->create([
            'status' => 'completed',
            'amount' => 100.00
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.refund', $transaction), [
                'amount' => 100.00,
                'reason' => 'Customer request'
            ]);

        $response->assertRedirect(route('admin.payments.transactions'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $transaction->id,
            'status' => 'refunded'
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_payment_management()
    {
        $user = User::factory()->create(['role' => 'user']);

        $routes = [
            'admin.payments.index',
            'admin.payments.create',
            'admin.payments.store',
            'admin.payments.edit',
            'admin.payments.update',
            'admin.payments.destroy',
            'admin.payments.toggle-status',
            'admin.payments.transactions',
            'admin.payments.transaction-details',
            'admin.payments.update-status',
            'admin.payments.refund'
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)
                ->get(route($route));

            $response->assertStatus(403);
        }
    }
} 