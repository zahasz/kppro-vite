<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wyłączam tymczasowo foreign key constraints, by móc wyczyścić tabele
        Schema::disableForeignKeyConstraints();
        
        // Wyczyść istniejące dane
        DB::table('subscription_payments')->truncate();
        DB::table('user_subscriptions')->truncate();
        DB::table('subscription_plans')->truncate();
        
        // Włączam z powrotem foreign key constraints
        Schema::enableForeignKeyConstraints();

        // 1. Dodaj plany subskrypcji
        $plans = [
            [
                'name' => 'Darmowy',
                'code' => 'free',
                'description' => 'Plan darmowy z podstawowymi funkcjami',
                'price' => 0,
                'billing_period' => 'lifetime',
                'max_invoices' => 10,
                'max_products' => 10,
                'max_contractors' => 5,
                'features' => json_encode(['finance_module']),
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Standard',
                'code' => 'standard',
                'description' => 'Plan standardowy z rozszerzonymi funkcjami',
                'price' => 49.00,
                'billing_period' => 'monthly',
                'max_invoices' => 100,
                'max_products' => 100,
                'max_contractors' => 50,
                'features' => json_encode(['finance_module', 'warehouse_module', 'estimates']),
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Premium',
                'code' => 'premium',
                'description' => 'Plan premium ze wszystkimi funkcjami',
                'price' => 99.00,
                'billing_period' => 'monthly',
                'max_invoices' => 1000,
                'max_products' => 1000,
                'max_contractors' => 500,
                'features' => json_encode(['finance_module', 'warehouse_module', 'contracts_module', 'estimates', 'advanced_reports', 'api_access']),
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'Premium Roczny',
                'code' => 'premium-yearly',
                'description' => 'Plan premium roczny (20% taniej)',
                'price' => 990.00,
                'billing_period' => 'yearly',
                'max_invoices' => 1000,
                'max_products' => 1000,
                'max_contractors' => 500,
                'features' => json_encode(['finance_module', 'warehouse_module', 'contracts_module', 'estimates', 'advanced_reports', 'api_access']),
                'is_active' => true,
                'display_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }

        // 2. Dodaj subskrypcje użytkowników
        $users = User::take(20)->get();
        if ($users->isEmpty()) {
            // Jeśli brak użytkowników, tworzy kilku przykładowych
            for ($i = 1; $i <= 20; $i++) {
                $users->push(User::create([
                    'name' => "Użytkownik $i",
                    'email' => "user$i@example.com",
                    'password' => bcrypt('password'),
                    'is_active' => true,
                ]));
            }
        }
        
        // Pobierz plany
        $freePlan = SubscriptionPlan::where('code', 'free')->first();
        $standardPlan = SubscriptionPlan::where('code', 'standard')->first();
        $premiumPlan = SubscriptionPlan::where('code', 'premium')->first();
        $premiumYearlyPlan = SubscriptionPlan::where('code', 'premium-yearly')->first();
        
        // Przypisz subskrypcje do użytkowników
        $statuses = ['active', 'pending', 'trial', 'expired', 'cancelled'];
        $paymentMethods = ['card', 'paypal', 'bank_transfer', null];
        
        foreach ($users as $index => $user) {
            // Wybierz plan dla użytkownika
            $planIndex = $index % 4;
            $plan = null;
            
            switch ($planIndex) {
                case 0:
                    $plan = $freePlan;
                    break;
                case 1:
                    $plan = $standardPlan;
                    break;
                case 2:
                    $plan = $premiumPlan;
                    break;
                case 3:
                    $plan = $premiumYearlyPlan;
                    break;
            }
            
            $status = $statuses[$index % count($statuses)];
            $paymentMethod = $plan->price > 0 ? $paymentMethods[$index % (count($paymentMethods) - 1)] : null;
            
            // Określ daty
            $startDate = Carbon::now()->subDays(rand(1, 90));
            $endDate = null;
            
            if ($plan->billing_period === 'monthly') {
                $endDate = $startDate->copy()->addMonth();
            } elseif ($plan->billing_period === 'yearly') {
                $endDate = $startDate->copy()->addYear();
            }
            
            // Jeśli status jest expired, ustaw datę końcową w przeszłości
            if ($status === 'expired') {
                $endDate = Carbon::now()->subDays(rand(1, 10));
            }
            
            // Utwórz subskrypcję
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_method' => $paymentMethod,
                'payment_details' => $paymentMethod ? 'Przykładowe dane płatności' : null,
            ]);
            
            // Dodaj płatności, jeśli plan jest płatny i status nie jest trial
            if ($plan->price > 0 && $status !== 'trial') {
                // Dodaj historyczną płatność
                if (in_array($status, ['active', 'expired', 'cancelled'])) {
                    $paymentStatus = $status === 'active' ? 'paid' : ($status === 'expired' ? 'failed' : 'refunded');
                    
                    SubscriptionPayment::create([
                        'user_id' => $user->id,
                        'user_subscription_id' => $subscription->id,
                        'transaction_id' => 'TXN' . str_pad(rand(1, 999999), 10, '0', STR_PAD_LEFT),
                        'amount' => $plan->price,
                        'status' => $paymentStatus,
                        'payment_method' => $paymentMethod,
                        'payment_details' => 'Przykładowe dane płatności',
                        'created_at' => $startDate,
                    ]);
                }
                
                // Dodaj ostatnią płatność dla aktywnych subskrypcji
                if ($status === 'active') {
                    $lastPaymentDate = Carbon::now()->subDays(rand(1, 28));
                    
                    SubscriptionPayment::create([
                        'user_id' => $user->id,
                        'user_subscription_id' => $subscription->id,
                        'transaction_id' => 'TXN' . str_pad(rand(1, 999999), 10, '0', STR_PAD_LEFT),
                        'amount' => $plan->price,
                        'status' => 'paid',
                        'payment_method' => $paymentMethod,
                        'payment_details' => 'Przykładowe dane płatności',
                        'created_at' => $lastPaymentDate,
                    ]);
                    
                    // Dodaj jeszcze kilka historycznych płatności dla wybranych użytkowników
                    if ($index % 3 === 0) {
                        for ($i = 2; $i <= 4; $i++) {
                            $histPaymentDate = Carbon::now()->subMonths($i);
                            
                            SubscriptionPayment::create([
                                'user_id' => $user->id,
                                'user_subscription_id' => $subscription->id,
                                'transaction_id' => 'TXN' . str_pad(rand(1, 999999), 10, '0', STR_PAD_LEFT),
                                'amount' => $plan->price,
                                'status' => 'paid',
                                'payment_method' => $paymentMethod,
                                'payment_details' => 'Przykładowe dane płatności',
                                'created_at' => $histPaymentDate,
                            ]);
                        }
                    }
                }
                
                // Dodaj błąd płatności dla niektórych użytkowników
                if ($index % 7 === 0) {
                    SubscriptionPayment::create([
                        'user_id' => $user->id,
                        'user_subscription_id' => $subscription->id,
                        'transaction_id' => 'TXN' . str_pad(rand(1, 999999), 10, '0', STR_PAD_LEFT),
                        'amount' => $plan->price,
                        'status' => 'failed',
                        'payment_method' => $paymentMethod,
                        'payment_details' => 'Błąd autoryzacji karty',
                        'created_at' => Carbon::now()->subDays(rand(1, 14)),
                    ]);
                }
            }
        }
    }
}
