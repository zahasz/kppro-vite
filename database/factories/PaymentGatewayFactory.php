<?php

namespace Database\Factories;

use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentGatewayFactory extends Factory
{
    protected $model = PaymentGateway::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company() . ' Gateway',
            'code' => $this->faker->unique()->slug(2),
            'class_name' => 'App\Gateways\TestGateway',
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'test_mode' => true,
            'display_order' => $this->faker->numberBetween(0, 10),
            'logo' => null,
            'config' => [
                'api_key' => Str::random(32),
                'api_secret' => Str::random(64),
            ],
        ];
    }
} 