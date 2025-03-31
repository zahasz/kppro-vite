<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BillingSettings;
use Carbon\Carbon;

class BillingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprawdź czy istnieją już ustawienia
        if (BillingSettings::count() === 0) {
            // Utwórz domyślne ustawienia
            BillingSettings::create([
                'auto_generate' => true,
                'generation_day' => 1,
                'invoice_prefix' => 'FV/',
                'invoice_suffix' => '/' . Carbon::now()->format('Y'),
                'reset_numbering' => true,
                'payment_days' => 14,
                'default_currency' => 'PLN',
                'default_tax_rate' => 23.00,
                'vat_number' => 'PL1234567890',
                'invoice_notes' => 'Dziękujemy za skorzystanie z naszych usług.',
                'email_notifications' => true,
            ]);
            
            $this->command->info('Utworzono domyślne ustawienia faktur');
        } else {
            $this->command->info('Ustawienia faktur już istnieją - pomijam');
        }
    }
} 