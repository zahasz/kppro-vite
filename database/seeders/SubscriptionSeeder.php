<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Models\CompanyProfile;
use App\Models\BankAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\SubscriptionService;
use ReflectionClass;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Tworzenie planów subskrypcyjnych...');
        
        // Twórz plany subskrypcji
        $this->createSubscriptionPlans();
        
        // Twórz przykładowe subskrypcje dla użytkowników
        $this->createSampleSubscriptions();
        
        $this->command->info('Seeder subskrypcji zakończony pomyślnie.');
    }

    /**
     * Twórz plany subskrypcyjne.
     */
    private function createSubscriptionPlans(): void
    {
        // Sprawdź czy już istnieją plany
        if (SubscriptionPlan::count() > 0) {
            $this->command->info('Plany subskrypcyjne już istnieją. Pomijam.');
            return;
        }

        $plans = [
            [
                'name' => 'Podstawowy',
                'code' => 'basic',
                'description' => 'Plan podstawowy dla małych firm. Dostęp do podstawowych funkcji systemu.',
                'price' => 49.00,
                'currency' => 'PLN',
                'billing_period' => 'monthly',
                'tax_rate' => 23,
                'features' => [
                    'invoices_basic',
                    'contractors_basic',
                    'products_basic'
                ],
                'max_invoices' => 50,
                'max_products' => 100,
                'max_clients' => 50,
                'is_active' => true,
                'display_order' => 1
            ],
            [
                'name' => 'Standard',
                'code' => 'standard',
                'description' => 'Plan standardowy dla rozwijających się firm. Rozszerzone funkcje i limity.',
                'price' => 99.00,
                'currency' => 'PLN',
                'billing_period' => 'monthly',
                'tax_rate' => 23,
                'features' => [
                    'invoices_advanced',
                    'contractors_advanced',
                    'products_advanced',
                    'warehouse_basic',
                    'financial_basic'
                ],
                'max_invoices' => 200,
                'max_products' => 500,
                'max_clients' => 200,
                'is_active' => true,
                'display_order' => 2
            ],
            [
                'name' => 'Business',
                'code' => 'business',
                'description' => 'Plan biznesowy dla średnich i dużych firm. Pełny dostęp do wszystkich funkcji.',
                'price' => 199.00,
                'currency' => 'PLN',
                'billing_period' => 'monthly',
                'tax_rate' => 23,
                'features' => [
                    'invoices_full',
                    'contractors_full',
                    'products_full',
                    'warehouse_full',
                    'financial_full',
                    'reports_full',
                    'api_access'
                ],
                'max_invoices' => 1000,
                'max_products' => 2000,
                'max_clients' => 1000,
                'is_active' => true,
                'display_order' => 3
            ],
            [
                'name' => 'Enterprise',
                'code' => 'enterprise',
                'description' => 'Plan dla dużych przedsiębiorstw. Nieograniczony dostęp i dedykowane wsparcie.',
                'price' => 499.00,
                'currency' => 'PLN',
                'billing_period' => 'monthly',
                'tax_rate' => 23,
                'features' => [
                    'invoices_full',
                    'contractors_full',
                    'products_full',
                    'warehouse_full',
                    'financial_full',
                    'reports_full',
                    'api_access',
                    'dedicated_support',
                    'custom_features'
                ],
                'max_invoices' => null, // brak limitu
                'max_products' => null, // brak limitu
                'max_clients' => null, // brak limitu
                'is_active' => true,
                'display_order' => 4
            ]
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::create($planData);
        }

        $this->command->info('Utworzono ' . count($plans) . ' planów subskrypcji.');
    }

    /**
     * Twórz przykładowe subskrypcje dla użytkowników
     */
    private function createSampleSubscriptions(): void
    {
        // Sprawdź czy użytkownicy istnieją
        $users = User::where('email', '!=', 'admin@kppro.pl')->take(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('Brak użytkowników do utworzenia przykładowych subskrypcji. Pomijam.');
            return;
        }

        // Sprawdź czy admin ma już subskrypcję
        $admin = User::where('email', 'admin@kppro.pl')->first();
        
        if ($admin && !UserSubscription::where('user_id', $admin->id)->exists()) {
            $this->createAdminSubscription($admin);
        }

        // Sprawdź czy użytkownicy już mają subskrypcje
        if (UserSubscription::count() > 1) {
            $this->command->info('Subskrypcje użytkowników już istnieją. Pomijam.');
            return;
        }

        // Pobierz plany subskrypcji
        $plans = SubscriptionPlan::where('is_active', true)->get();
        
        if ($plans->isEmpty()) {
            $this->command->info('Brak aktywnych planów subskrypcji. Nie można utworzyć przykładowych subskrypcji.');
            return;
        }

        $this->command->info('Tworzenie przykładowych subskrypcji dla użytkowników...');

        // Przypisz losowo plany do użytkowników
        foreach ($users as $index => $user) {
            // Sprawdź czy użytkownik już ma subskrypcję
            if (UserSubscription::where('user_id', $user->id)->exists()) {
                continue;
            }

            // Wybierz losowy plan
            $plan = $plans->random();

            // Sprawdź czy użytkownik ma profil firmy
            if (!$user->companyProfile) {
                $this->createCompanyProfile($user);
            }

            // Utwórz subskrypcję
            $now = Carbon::now();
            $subscription = new UserSubscription();
            $subscription->user_id = $user->id;
            $subscription->subscription_plan_id = $plan->id;
            $subscription->status = 'active';
            $subscription->price = $plan->price;
            $subscription->start_date = $now;
            $subscription->end_date = $now->copy()->addMonth();
            $subscription->subscription_type = 'automatic';
            $subscription->renewal_status = 'enabled';
            $subscription->next_billing_date = $now->copy()->addMonth();
            $subscription->payment_method = 'card';
            $subscription->payment_details = 'Karta płatnicza: **** **** **** ' . rand(1000, 9999);
            $subscription->save();

            // Utwórz płatność
            $payment = new SubscriptionPayment();
            $payment->user_id = $user->id;
            $payment->user_subscription_id = $subscription->id;
            $payment->transaction_id = 'sample-' . uniqid();
            $payment->amount = $plan->price;
            $payment->currency = $plan->currency;
            $payment->status = 'completed';
            $payment->payment_method = 'card';
            $payment->payment_details = 'Testowa płatność kartą';
            $payment->save();

            // Generuj fakturę za pomocą serwisu
            try {
                $subscriptionService = app(SubscriptionService::class);
                $reflection = new ReflectionClass($subscriptionService);
                $method = $reflection->getMethod('generateInvoiceForPayment');
                $method->setAccessible(true);
                $invoice = $method->invoke($subscriptionService, $payment);

                if ($invoice) {
                    $subscription->last_invoice_id = $invoice->id;
                    $subscription->last_invoice_number = $invoice->number;
                    $subscription->save();
                }
            } catch (\Exception $e) {
                $this->command->error('Błąd przy generowaniu faktury: ' . $e->getMessage());
            }
        }

        $this->command->info('Utworzono przykładowe subskrypcje dla użytkowników.');
    }

    /**
     * Utwórz subskrypcję dla administratora
     */
    private function createAdminSubscription(User $admin): void
    {
        // Znajdź plan business
        $plan = SubscriptionPlan::where('code', 'business')->first();
        
        if (!$plan) {
            $plan = SubscriptionPlan::where('is_active', true)->first();
        }

        if (!$plan) {
            $this->command->error('Brak dostępnych planów subskrypcji dla administratora.');
            return;
        }

        // Sprawdź czy admin ma profil firmy
        if (!$admin->companyProfile) {
            $this->createCompanyProfile($admin);
        }

        // Utwórz subskrypcję
        $now = Carbon::now();
        $subscription = new UserSubscription();
        $subscription->user_id = $admin->id;
        $subscription->subscription_plan_id = $plan->id;
        $subscription->status = 'active';
        $subscription->price = 0; // Bezpłatna subskrypcja dla administratora
        $subscription->start_date = $now;
        $subscription->end_date = $now->copy()->addYears(10); // Długi okres ważności
        $subscription->subscription_type = 'manual';
        $subscription->renewal_status = null;
        $subscription->payment_method = 'manual';
        $subscription->payment_details = 'Subskrypcja administratora systemu';
        $subscription->admin_notes = 'Automatycznie utworzona subskrypcja dla administratora systemu';
        $subscription->save();

        $this->command->info('Utworzono subskrypcję administratora.');
    }

    /**
     * Utwórz profil firmy dla użytkownika
     */
    private function createCompanyProfile(User $user): void
    {
        // Utwórz profil firmy
        $companyProfile = new CompanyProfile();
        $companyProfile->user_id = $user->id;
        $companyProfile->company_name = 'Firma ' . $user->name;
        $companyProfile->tax_number = '123456' . rand(1000, 9999);
        $companyProfile->regon = '12345' . rand(10000, 99999);
        $companyProfile->street = 'ul. Przykładowa ' . rand(1, 100);
        $companyProfile->city = 'Warszawa';
        $companyProfile->postal_code = '00-' . rand(100, 999);
        $companyProfile->phone = '+48 ' . rand(100, 999) . ' ' . rand(100, 999) . ' ' . rand(100, 999);
        $companyProfile->email = $user->email;
        $companyProfile->website = 'https://example.com';
        $companyProfile->invoice_payment_days = 14;
        $companyProfile->invoice_next_number = 1;
        $companyProfile->invoice_numbering_pattern = 'FV/{YEAR}/{MONTH}/{NUMBER}';
        $companyProfile->save();

        // Utwórz konto bankowe
        $bankAccount = new BankAccount();
        $bankAccount->company_profile_id = $companyProfile->id;
        $bankAccount->account_name = 'Konto główne';
        $bankAccount->account_number = 'PL' . rand(10, 99) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999);
        $bankAccount->bank_name = 'Bank Przykładowy S.A.';
        $bankAccount->swift = 'BPPLPLPW';
        $bankAccount->is_default = true;
        $bankAccount->save();

        // Przypisz konto bankowe jako domyślne
        $companyProfile->default_bank_account_id = $bankAccount->id;
        $companyProfile->save();
    }
}
