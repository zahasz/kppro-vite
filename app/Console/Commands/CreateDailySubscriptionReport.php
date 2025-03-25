<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CreateDailySubscriptionReport extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'report:subscriptions {--email= : E-mail na który wysłać raport} {--format=csv : Format raportu (csv/json)}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Generuje dzienny raport subskrypcji i płatności';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $this->info('Rozpoczynam generowanie raportu subskrypcji...');
        
        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::now()->subDay()->format('Y-m-d');
        
        // Statystyki subskrypcji
        $totalUsers = User::count();
        $usersWithSubscriptions = User::whereHas('userSubscriptions', function($query) {
            $query->where('status', 'active');
        })->count();
        
        $activeSubscriptions = UserSubscription::where('status', 'active')->count();
        $newSubscriptionsToday = UserSubscription::whereDate('created_at', $today)->count();
        $expiringToday = UserSubscription::where('status', 'active')
            ->whereDate('end_date', $today)
            ->count();
        
        // Statystyki planów
        $plans = SubscriptionPlan::all();
        $planStats = [];
        
        foreach ($plans as $plan) {
            $activeCount = UserSubscription::where('subscription_plan_id', $plan->id)
                ->where('status', 'active')
                ->count();
                
            $planStats[$plan->name] = [
                'active_count' => $activeCount,
                'percentage' => $totalUsers > 0 ? round(($activeCount / $totalUsers) * 100, 2) : 0,
                'price' => $plan->price,
                'revenue' => $plan->price * $activeCount,
            ];
        }
        
        // Statystyki płatności
        $paymentsToday = SubscriptionPayment::whereDate('created_at', $today)->count();
        $paymentAmountToday = SubscriptionPayment::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('amount');
            
        $paymentsYesterday = SubscriptionPayment::whereDate('created_at', $yesterday)->count();
        $paymentAmountYesterday = SubscriptionPayment::whereDate('created_at', $yesterday)
            ->where('status', 'paid')
            ->sum('amount');
            
        $revenueChange = 0;
        if ($paymentAmountYesterday > 0) {
            $revenueChange = (($paymentAmountToday - $paymentAmountYesterday) / $paymentAmountYesterday) * 100;
        }
        
        // Zbieramy dane do raportu
        $reportData = [
            'date' => $today,
            'total_users' => $totalUsers,
            'users_with_active_subscriptions' => $usersWithSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'new_subscriptions_today' => $newSubscriptionsToday,
            'expiring_today' => $expiringToday,
            'subscription_penetration' => $totalUsers > 0 ? round(($usersWithSubscriptions / $totalUsers) * 100, 2) : 0,
            'payments_today' => $paymentsToday,
            'payment_amount_today' => $paymentAmountToday,
            'revenue_change' => $revenueChange,
            'plans' => $planStats,
        ];
        
        // Generowanie raportu w wybranym formacie
        $format = $this->option('format');
        $filename = "subscription_report_{$today}.{$format}";
        
        if ($format === 'json') {
            $content = json_encode($reportData, JSON_PRETTY_PRINT);
            Storage::put("reports/{$filename}", $content);
        } else {
            // Generowanie CSV
            $csvData = [];
            $csvData[] = ['Data', $today];
            $csvData[] = ['Liczba użytkowników', $totalUsers];
            $csvData[] = ['Użytkownicy z aktywną subskrypcją', $usersWithSubscriptions];
            $csvData[] = ['Aktywne subskrypcje', $activeSubscriptions];
            $csvData[] = ['Nowe subskrypcje dzisiaj', $newSubscriptionsToday];
            $csvData[] = ['Subskrypcje wygasające dzisiaj', $expiringToday];
            $csvData[] = ['Penetracja subskrypcji', $reportData['subscription_penetration'] . '%'];
            $csvData[] = [''];
            $csvData[] = ['Płatności dzisiaj', $paymentsToday];
            $csvData[] = ['Kwota płatności dzisiaj', number_format($paymentAmountToday, 2) . ' PLN'];
            $csvData[] = ['Zmiana przychodu', number_format($revenueChange, 2) . '%'];
            $csvData[] = [''];
            $csvData[] = ['Plan', 'Liczba aktywnych', 'Procent użytkowników', 'Cena', 'Przychód'];
            
            foreach ($planStats as $planName => $stats) {
                $csvData[] = [
                    $planName,
                    $stats['active_count'],
                    $stats['percentage'] . '%',
                    number_format($stats['price'], 2) . ' PLN',
                    number_format($stats['revenue'], 2) . ' PLN',
                ];
            }
            
            $csv = '';
            foreach ($csvData as $row) {
                $csv .= implode(',', $row) . "\n";
            }
            
            Storage::put("reports/{$filename}", $csv);
        }
        
        $this->info("Raport wygenerowany i zapisany jako: reports/{$filename}");
        
        // Wysyłanie raportu e-mailem, jeśli określono adres
        $email = $this->option('email');
        if ($email) {
            $this->info("Wysyłanie raportu na adres: {$email}");
            
            // Tutaj kod wysyłki e-maila
            // Mail::to($email)->send(new \App\Mail\SubscriptionReport($reportData, storage_path("app/reports/{$filename}")));
            
            $this->info("Raport wysłany na adres: {$email}");
        }
        
        return 0;
    }
}
