<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\CompanyProfile;
use App\Models\BankAccount;
use App\Models\Contractor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Hash;

class FreshTestDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fresh:testdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wykonuje migracje od nowa i wypełnia bazę danych przykładowymi danymi testowymi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!app()->environment('local', 'development')) {
            $this->error('Ta komenda może być uruchamiana tylko w środowisku lokalnym lub deweloperskim!');
            return 1;
        }

        $this->info('Rozpoczynam tworzenie testowej bazy danych...');

        // Wykonujemy migracje od nowa
        $this->info('Wykonuję migracje od nowa...');
        Artisan::call('migrate:fresh');
        $this->info('Migracje zakończone pomyślnie.');

        // Tworzymy testowego użytkownika
        $this->info('Tworzę testowego użytkownika...');
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->info('Użytkownik testowy utworzony.');

        // Tworzymy profil firmy
        $this->info('Tworzę profil firmy...');
        $companyProfile = CompanyProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Test Company',
            'legal_form' => 'sole_proprietorship',
            'tax_number' => '1234567890',
            'regon' => '123456789',
            'street' => 'Test Street 123',
            'city' => 'Test City',
            'postal_code' => '12-345',
            'state' => 'mazowieckie',
            'country' => 'Polska',
            'phone' => '+48 123 456 789',
            'email' => 'company@example.com',
            'website' => 'https://example.com',
            'bank_name' => 'Test Bank',
            'bank_account' => 'PL 00 1234 5678 9012 3456 7890 1234',
            'swift' => 'TESTPLP0',
            'invoice_prefix' => 'FV',
            'invoice_numbering_pattern' => '{NUMBER}/{MONTH}/{YEAR}',
            'invoice_next_number' => 1,
            'invoice_payment_days' => 14,
            'default_payment_method' => 'przelew',
            'default_currency' => 'PLN',
        ]);
        $this->info('Profil firmy utworzony.');

        // Tworzymy konta bankowe
        $this->info('Tworzę konta bankowe...');
        $bankAccount1 = BankAccount::create([
            'company_profile_id' => $companyProfile->id,
            'account_name' => 'Konto główne PLN',
            'account_number' => 'PL 11 1111 2222 3333 4444 5555 6666',
            'bank_name' => 'Bank Polski',
            'swift' => 'BANKPLP0',
            'is_default' => true,
        ]);

        $bankAccount2 = BankAccount::create([
            'company_profile_id' => $companyProfile->id,
            'account_name' => 'Konto EUR',
            'account_number' => 'PL 22 1111 2222 3333 4444 5555 6666',
            'bank_name' => 'Bank Polski',
            'swift' => 'BANKPLP0',
            'is_default' => false,
        ]);

        // Ustawiamy domyślne konto bankowe
        $companyProfile->default_bank_account_id = $bankAccount1->id;
        $companyProfile->save();
        
        $this->info('Konta bankowe utworzone.');

        // Tworzymy kontrahentów
        $this->info('Tworzę kontrahentów...');
        $contractor1 = Contractor::create([
            'user_id' => $user->id,
            'company_name' => 'Firma ABC',
            'nip' => '5555555555',
            'regon' => '123456789',
            'street' => 'Ulica Biznesowa 1',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'country' => 'Polska',
            'phone' => '+48 111 222 333',
            'email' => 'kontakt@firmaabc.pl',
            'bank_name' => 'Bank Testowy',
            'bank_account_number' => 'PL 33 1111 2222 3333 4444 5555 6666',
            'swift_code' => 'TESTPL',
            'notes' => 'Przykładowy kontrahent',
            'status' => 'active',
        ]);

        $contractor2 = Contractor::create([
            'user_id' => $user->id,
            'company_name' => 'Przedsiębiorstwo XYZ',
            'nip' => '6666666666',
            'regon' => '987654321',
            'street' => 'Ulica Handlowa 10',
            'city' => 'Kraków',
            'postal_code' => '30-001',
            'country' => 'Polska',
            'phone' => '+48 444 555 666',
            'email' => 'biuro@xyz.pl',
            'bank_name' => 'Bank Komercyjny',
            'bank_account_number' => 'PL 44 1111 2222 3333 4444 5555 6666',
            'swift_code' => 'KOMPL',
            'notes' => 'Drugi przykładowy kontrahent',
            'status' => 'active',
        ]);

        $this->info('Kontrahenci utworzeni.');

        // Tworzymy faktury
        $this->info('Tworzę faktury...');
        
        $invoice1 = Invoice::create([
            'user_id' => $user->id,
            'number' => 'FV/001/05/2025',
            'contractor_id' => $contractor1->id,
            'contractor_name' => $contractor1->company_name,
            'contractor_nip' => $contractor1->nip,
            'contractor_address' => $contractor1->street . ', ' . $contractor1->postal_code . ' ' . $contractor1->city,
            'issue_date' => now()->format('Y-m-d'),
            'sale_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'payment_method' => 'przelew',
            'bank_account_id' => $bankAccount1->id,
            'currency' => 'PLN',
            'net_total' => 1000.00,
            'tax_total' => 230.00,
            'gross_total' => 1230.00,
            'status' => 'issued',
        ]);
        
        // Dodajemy pozycje faktury
        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'name' => 'Usługa programistyczna',
            'quantity' => 10,
            'unit' => 'godz.',
            'unit_price' => 100.00,
            'tax_rate' => 23,
            'net_price' => 1000.00,
            'tax_amount' => 230.00,
            'gross_price' => 1230.00,
        ]);
        
        // Druga faktura z innym kontem bankowym
        $invoice2 = Invoice::create([
            'user_id' => $user->id,
            'number' => 'FV/002/05/2025',
            'contractor_id' => $contractor2->id,
            'contractor_name' => $contractor2->company_name,
            'contractor_nip' => $contractor2->nip,
            'contractor_address' => $contractor2->street . ', ' . $contractor2->postal_code . ' ' . $contractor2->city,
            'issue_date' => now()->format('Y-m-d'),
            'sale_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'payment_method' => 'przelew',
            'bank_account_id' => $bankAccount2->id,
            'currency' => 'EUR',
            'net_total' => 500.00,
            'tax_total' => 115.00,
            'gross_total' => 615.00,
            'status' => 'issued',
        ]);
        
        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'name' => 'Konsultacje',
            'quantity' => 5,
            'unit' => 'godz.',
            'unit_price' => 100.00,
            'tax_rate' => 23,
            'net_price' => 500.00,
            'tax_amount' => 115.00,
            'gross_price' => 615.00,
        ]);

        $this->info('Faktury utworzone.');
        
        $this->info('Testowa baza danych została pomyślnie utworzona!');
        $this->info('');
        $this->info('Dane do logowania:');
        $this->info('Email: test@example.com');
        $this->info('Hasło: password');
        
        return 0;
    }
}
