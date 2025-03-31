<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CompanyProfile;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Tworzenie konta administratora...');

        // Sprawdź czy role istnieją, jeśli nie - utwórz je
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin', 'guard_name' => 'web']);
            $this->command->info('Utworzono rolę admin');
        }
        
        if (!Role::where('name', 'user')->exists()) {
            Role::create(['name' => 'user', 'guard_name' => 'web']);
            $this->command->info('Utworzono rolę user');
        }

        // Utwórz lub zaktualizuj konto administratora
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

        // Tworzenie lub aktualizacja profilu firmy dla administratora
        if (!$admin->companyProfile) {
            $companyProfile = new CompanyProfile();
            $companyProfile->user_id = $admin->id;
            $companyProfile->company_name = 'KPPRO Sp. z o.o.';
            $companyProfile->legal_form = 'limited_liability';
            $companyProfile->tax_number = '1234567890';
            $companyProfile->regon = '123456789';
            $companyProfile->krs = '0000123456';
            $companyProfile->street = 'ul. Przykładowa 123';
            $companyProfile->city = 'Warszawa';
            $companyProfile->state = 'mazowieckie';
            $companyProfile->country = 'Polska';
            $companyProfile->postal_code = '00-001';
            $companyProfile->phone = '+48 123 456 789';
            $companyProfile->phone_additional = '+48 987 654 321';
            $companyProfile->email = 'biuro@kppro.pl';
            $companyProfile->email_additional = 'kontakt@kppro.pl';
            $companyProfile->website = 'https://kppro.pl';
            $companyProfile->bank_name = 'Bank Przykładowy S.A.';
            $companyProfile->bank_account = 'PL 12 1234 5678 9012 3456 7890 1234';
            $companyProfile->swift = 'PKPLPLPW';
            $companyProfile->notes = 'Przykładowe notatki o firmie';
            // Dane dotyczące faktur VAT
            $companyProfile->invoice_prefix = '';
            $companyProfile->invoice_numbering_pattern = 'FV/{YEAR}/{MONTH}/{NUMBER}';
            $companyProfile->invoice_next_number = 1;
            $companyProfile->invoice_payment_days = 14;
            $companyProfile->default_payment_method = 'przelew';
            $companyProfile->default_currency = 'PLN';
            $companyProfile->invoice_notes = 'Dziękujemy za skorzystanie z naszych usług.';
            $companyProfile->invoice_footer = 'Faktura wystawiona elektronicznie, nie wymaga podpisu.';
            $companyProfile->save();

            // Dodawanie konta bankowego
            $bankAccount = new BankAccount();
            $bankAccount->company_profile_id = $companyProfile->id;
            $bankAccount->account_name = 'Konto główne';
            $bankAccount->account_number = 'PL 12 1234 5678 9012 3456 7890 1234';
            $bankAccount->bank_name = 'Bank Przykładowy S.A.';
            $bankAccount->swift = 'PKPLPLPW';
            $bankAccount->is_default = true;
            $bankAccount->save();

            // Ustawienie domyślnego konta bankowego
            $companyProfile->default_bank_account_id = $bankAccount->id;
            $companyProfile->save();

            $this->command->info('Utworzono profil firmy dla administratora');
        }

        // Tworzenie konta testowego
        $testUser = User::firstOrCreate(
            ['email' => 'test@kppro.pl'],
            [
                'name' => 'Konto Testowe',
                'first_name' => 'Konto',
                'last_name' => 'Testowe',
                'password' => Hash::make('Test123!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'language' => 'pl',
                'timezone' => 'Europe/Warsaw'
            ]
        );

        if (!$testUser->hasRole('user')) {
            $testUser->assignRole('user');
        }

        $this->command->info('Konta administratora i testowe zostały pomyślnie utworzone.');
    }
} 