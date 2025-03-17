<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@kppro.pl'],
            [
                'name' => 'Administrator',
                'first_name' => 'Admin',
                'last_name' => 'Istrator',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'language' => 'pl',
                'timezone' => 'Europe/Warsaw'
            ]
        );

        // Przypisanie roli admin do użytkownika
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Tworzenie lub aktualizacja profilu firmy
        $companyProfile = CompanyProfile::firstOrNew(['user_id' => $admin->id]);
        $companyProfile->fill([
            'company_name' => 'KPPRO Sp. z o.o.',
            'legal_form' => 'limited_liability',
            'tax_number' => '1234567890',
            'regon' => '123456789',
            'krs' => '0000123456',
            'street' => 'ul. Przykładowa 123',
            'city' => 'Warszawa',
            'state' => 'mazowieckie',
            'country' => 'Polska',
            'postal_code' => '00-001',
            'phone' => '+48 123 456 789',
            'phone_additional' => '+48 987 654 321',
            'email' => 'biuro@kppro.pl',
            'email_additional' => 'kontakt@kppro.pl',
            'website' => 'https://kppro.pl',
            'bank_name' => 'Bank Przykładowy S.A.',
            'bank_account' => 'PL 12 1234 5678 9012 3456 7890 1234',
            'swift' => 'PKPLPLPW',
            'notes' => 'Przykładowe notatki o firmie',
            // Dane dotyczące faktur VAT
            'invoice_prefix' => '',
            'invoice_numbering_pattern' => 'FV/{YEAR}/{MONTH}/{NUMBER}',
            'invoice_next_number' => 1,
            'invoice_payment_days' => 14,
            'default_payment_method' => 'przelew',
            'default_currency' => 'PLN',
            'invoice_notes' => 'Dziękujemy za skorzystanie z naszych usług.',
            'invoice_footer' => 'Faktura wystawiona elektronicznie, nie wymaga podpisu.'
        ]);
        $admin->companyProfile()->save($companyProfile);

        $this->command->info('Administrator został utworzony pomyślnie.');
    }
} 