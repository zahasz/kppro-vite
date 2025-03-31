<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingSettings extends Model
{
    use HasFactory;

    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array
     */
    protected $fillable = [
        'auto_generate',
        'generation_day',
        'invoice_prefix',
        'invoice_suffix',
        'reset_numbering',
        'payment_days',
        'default_currency',
        'default_tax_rate',
        'vat_number',
        'invoice_notes',
        'email_notifications',
    ];

    /**
     * Atrybuty, które powinny być rzutowane na typy.
     *
     * @var array
     */
    protected $casts = [
        'auto_generate' => 'boolean',
        'generation_day' => 'integer',
        'reset_numbering' => 'boolean',
        'payment_days' => 'integer',
        'default_tax_rate' => 'float',
        'email_notifications' => 'boolean',
    ];

    /**
     * Pobiera aktywne ustawienia faktur.
     *
     * @return self
     */
    public static function getActive()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'auto_generate' => true,
                'generation_day' => 1,
                'payment_days' => 14,
                'default_currency' => 'PLN',
                'default_tax_rate' => 23.00,
                'email_notifications' => true,
            ]);
        }
        
        return $settings;
    }
}
