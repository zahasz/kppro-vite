<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSettings extends Model
{
    use HasFactory;

    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array
     */
    protected $fillable = [
        'auto_retry_failed_payments',
        'payment_retry_attempts',
        'payment_retry_interval',
        'grace_period_days',
        'default_payment_gateway',
        'renewal_notifications',
        'renewal_notification_days',
        'auto_cancel_after_failed_payments',
        'renewal_charge_days_before',
        'enable_accounting_integration',
        'accounting_api_url',
        'accounting_api_key',
    ];

    /**
     * Atrybuty, które powinny być rzutowane na typy.
     *
     * @var array
     */
    protected $casts = [
        'auto_retry_failed_payments' => 'boolean',
        'payment_retry_attempts' => 'integer',
        'payment_retry_interval' => 'integer',
        'grace_period_days' => 'integer',
        'renewal_notifications' => 'boolean',
        'renewal_notification_days' => 'integer',
        'auto_cancel_after_failed_payments' => 'boolean',
        'renewal_charge_days_before' => 'integer',
        'enable_accounting_integration' => 'boolean',
    ];

    /**
     * Pobiera aktywne ustawienia płatności.
     *
     * @return self
     */
    public static function getActive()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'auto_retry_failed_payments' => true,
                'payment_retry_attempts' => 3,
                'payment_retry_interval' => 3,
                'grace_period_days' => 3,
                'renewal_notifications' => true,
                'renewal_notification_days' => 7,
                'auto_cancel_after_failed_payments' => true,
                'renewal_charge_days_before' => 3,
                'enable_accounting_integration' => false,
            ]);
        }
        
        return $settings;
    }
} 