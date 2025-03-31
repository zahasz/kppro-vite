<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentSettings;

class PaymentSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuń wszystkie istniejące ustawienia płatności, aby uniknąć duplikatów
        PaymentSettings::query()->delete();
        
        // Utwórz domyślne ustawienia płatności
        PaymentSettings::create([
            'auto_retry_failed_payments' => true,
            'payment_retry_attempts' => 3,
            'payment_retry_interval' => 3,
            'grace_period_days' => 3,
            'default_payment_gateway' => 'test_gateway',
            'renewal_notifications' => true,
            'renewal_notification_days' => 7,
            'auto_cancel_after_failed_payments' => true,
            'renewal_charge_days_before' => 3,
            'enable_accounting_integration' => false,
            'accounting_api_url' => null,
            'accounting_api_key' => null,
        ]);
        
        $this->command->info('Ustawienia płatności zostały pomyślnie zainicjowane.');
    }
}
