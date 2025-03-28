<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Upewnijmy się, że mamy jakieś plany subskrypcji
        if (Plan::count() === 0) {
            // Utwórz podstawowe plany, jeśli nie istnieją
            Plan::create([
                'name' => 'Basic',
                'description' => 'Plan podstawowy z ograniczonymi funkcjami',
                'price' => 29.99,
                'interval' => 'monthly',
                'subscription_type' => 'both',
                'is_active' => true,
            ]);
            
            Plan::create([
                'name' => 'Premium',
                'description' => 'Plan premium ze wszystkimi funkcjami',
                'price' => 99.99,
                'interval' => 'monthly',
                'subscription_type' => 'both',
                'is_active' => true,
            ]);
            
            Plan::create([
                'name' => 'Business',
                'description' => 'Plan biznesowy z dedykowanym wsparciem',
                'price' => 199.99,
                'interval' => 'monthly',
                'subscription_type' => 'both',
                'is_active' => true,
            ]);
        }
        
        // Upewnijmy się, że mamy użytkowników
        if (User::count() <= 1) { // Tylko administrator
            for ($i = 1; $i <= 10; $i++) {
                User::create([
                    'name' => "Użytkownik Testowy $i",
                    'email' => "user$i@example.com",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
            }
        }
        
        // Wyczyść istniejące subskrypcje - użyj delete zamiast truncate ze względu na klucze obce
        // Wcześniej usuń powiązane rekordy w tabeli invoices
        DB::table('invoices')->where('subscription_id', '>', 0)->delete();
        Subscription::query()->delete();
        
        // Pobierz istniejące plany i użytkowników
        $plans = Plan::all();
        $users = User::where('id', '>', 1)->take(10)->get(); // Pomijamy administratora
        
        // Utwórz przykładowe subskrypcje
        foreach ($users as $index => $user) {
            // Wybierz losowy plan
            $plan = $plans->random();
            
            // Co drugi użytkownik ma subskrypcję automatyczną, reszta ręczną
            $subscriptionType = $index % 2 === 0 ? Subscription::TYPE_AUTOMATIC : Subscription::TYPE_MANUAL;
            
            // Ustaw status odnowienia tylko dla automatycznych subskrypcji
            $renewalStatus = $subscriptionType === Subscription::TYPE_AUTOMATIC ? Subscription::RENEWAL_ENABLED : null;
            
            // Utwórz subskrypcję
            $startDate = Carbon::now()->subDays(rand(1, 30));
            
            // Ustal datę końcową na podstawie planu
            if ($plan->interval === 'monthly') {
                $endDate = $startDate->copy()->addMonth();
            } elseif ($plan->interval === 'quarterly') {
                $endDate = $startDate->copy()->addMonths(3);
            } elseif ($plan->interval === 'biannually') {
                $endDate = $startDate->copy()->addMonths(6);
            } elseif ($plan->interval === 'annually') {
                $endDate = $startDate->copy()->addYear();
            } else {
                $endDate = null; // Dla bezterminowych
            }
            
            // Ustaw datę następnej płatności dla automatycznych subskrypcji
            $nextPaymentDate = $subscriptionType === Subscription::TYPE_AUTOMATIC ? $endDate : null;
            
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'price' => $plan->price,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'subscription_type' => $subscriptionType,
                'renewal_status' => $renewalStatus,
                'next_payment_date' => $nextPaymentDate,
                'payment_method' => $subscriptionType === Subscription::TYPE_AUTOMATIC ? 'card' : 'transfer',
            ]);
        }
        
        // Dodaj kilka subskrypcji w innym statusie
        $statuses = ['pending', 'cancelled', 'expired'];
        
        foreach ($statuses as $status) {
            foreach ($users->take(3) as $user) {
                $plan = $plans->random();
                $subscriptionType = rand(0, 1) === 0 ? Subscription::TYPE_AUTOMATIC : Subscription::TYPE_MANUAL;
                $renewalStatus = $subscriptionType === Subscription::TYPE_AUTOMATIC ? Subscription::RENEWAL_ENABLED : null;
                
                $startDate = Carbon::now()->subDays(rand(30, 60));
                
                if ($plan->interval === 'monthly') {
                    $endDate = $startDate->copy()->addMonth();
                } elseif ($plan->interval === 'quarterly') {
                    $endDate = $startDate->copy()->addMonths(3);
                } elseif ($plan->interval === 'biannually') {
                    $endDate = $startDate->copy()->addMonths(6);
                } elseif ($plan->interval === 'annually') {
                    $endDate = $startDate->copy()->addYear();
                } else {
                    $endDate = null;
                }
                
                $cancelled_at = $status === 'cancelled' ? Carbon::now()->subDays(rand(1, 10)) : null;
                
                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'status' => $status,
                    'price' => $plan->price,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'subscription_type' => $subscriptionType,
                    'renewal_status' => $renewalStatus,
                    'next_payment_date' => null,
                    'payment_method' => $subscriptionType === Subscription::TYPE_AUTOMATIC ? 'card' : 'transfer',
                    'cancelled_at' => $cancelled_at,
                ]);
            }
        }
    }
}
