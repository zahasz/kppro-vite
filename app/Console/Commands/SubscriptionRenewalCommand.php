<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Services\SubscriptionService;
use App\Services\PaymentGatewayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\SubscriptionRenewed;
use App\Notifications\RenewalFailed;

class SubscriptionRenewalCommand extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:renewal 
                            {--dry-run : Tryb testowy bez wykonywania faktycznych płatności i zmian}
                            {--days-before=3 : Liczba dni przed wygaśnięciem, kiedy następuje próba odnowienia}
                            {--limit=50 : Maksymalna liczba subskrypcji do przetworzenia}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Automatycznie odnawia subskrypcje, których data wygaśnięcia zbliża się';

    /**
     * Usługa subskrypcji
     */
    protected $subscriptionService;

    /**
     * Usługa bramki płatności
     */
    protected $paymentGatewayService;

    /**
     * Konstruktor
     */
    public function __construct(SubscriptionService $subscriptionService, PaymentGatewayService $paymentGatewayService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
        $this->paymentGatewayService = $paymentGatewayService;
    }

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        // Pobierz ustawienia systemowe jeśli nie są podane przez argumenty
        $paymentSettings = \App\Models\PaymentSettings::getActive();
        
        // Użyj parametru z linii komend lub wartości z ustawień
        $daysBefore = (int) $this->option('days-before') ?: $paymentSettings->renewal_charge_days_before ?? 3;
        $limit = (int) $this->option('limit');
        
        if ($dryRun) {
            $this->info("Uruchomiono w trybie testowym (dry-run) - płatności NIE będą wykonywane.");
        }
        
        $this->info("Rozpoczynam proces automatycznego odnawiania subskrypcji (na {$daysBefore} dni przed wygaśnięciem)...");
        
        // Data, według której sprawdzamy subskrypcje do odnowienia
        $renewDate = Carbon::now()->addDays($daysBefore);
        
        // Pobierz subskrypcje do odnowienia
        $subscriptionsToRenew = UserSubscription::where('status', 'active')
            ->where(function ($query) {
                $query->where('auto_renew', true)
                      ->orWhere('renewal_status', UserSubscription::RENEWAL_ENABLED);
            })
            ->whereDate('end_date', '=', $renewDate->toDateString())
            ->whereNotNull('end_date')
            ->whereNotIn('payment_method', ['free', 'manual'])
            ->limit($limit)
            ->get();
            
        $count = $subscriptionsToRenew->count();
        
        if ($count === 0) {
            $this->info("Nie znaleziono subskrypcji do odnowienia.");
            return 0;
        }
        
        $this->info("Znaleziono {$count} subskrypcji do odnowienia.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($subscriptionsToRenew as $subscription) {
            if (!$dryRun) {
                DB::beginTransaction();
            }
            
            try {
                $user = $subscription->user;
                $plan = $subscription->subscriptionPlan;
                
                if (!$user || !$plan) {
                    throw new \Exception("Nie znaleziono użytkownika lub planu subskrypcji.");
                }
                
                $this->line("");
                $this->info("Przetwarzanie odnowienia subskrypcji {$plan->name} dla użytkownika {$user->email}");
                
                // Pobierz dane płatności
                $paymentMethod = $subscription->payment_method;
                $paymentDetails = $subscription->payment_details;
                
                // Przygotuj dane do odnowienia
                $amount = $plan->price;
                $currency = $plan->currency ?? 'PLN';
                $description = "Odnowienie subskrypcji {$plan->name}";
                
                if (!$dryRun) {
                    // Wykonaj płatność za pomocą usługi bramki płatności
                    $paymentResult = $this->paymentGatewayService->processRenewal(
                        $user,
                        $amount,
                        $currency,
                        $paymentMethod,
                        $paymentDetails,
                        $description,
                        $subscription
                    );
                    
                    if ($paymentResult['success']) {
                        // Oblicz nową datę wygaśnięcia
                        $newEndDate = $this->calculateNewEndDate($subscription->end_date, $plan->billing_period);
                        
                        // Aktualizuj subskrypcję
                        $subscription->end_date = $newEndDate;
                        $subscription->save();
                        
                        // Utwórz rekord płatności
                        Payment::create([
                            'user_id' => $user->id,
                            'user_subscription_id' => $subscription->id,
                            'transaction_id' => $paymentResult['transaction_id'],
                            'amount' => $amount,
                            'currency' => $currency,
                            'status' => 'completed',
                            'payment_method' => $paymentMethod,
                            'payment_details' => json_encode($paymentResult['details'] ?? []),
                            'payment_date' => now(),
                            'description' => $description,
                        ]);
                        
                        // Powiadom użytkownika o odnowieniu
                        $user->notify(new SubscriptionRenewed($subscription, $newEndDate));
                        
                        $this->info("Pomyślnie odnowiono subskrypcję dla użytkownika {$user->email} do {$newEndDate->format('Y-m-d')}");
                        $successCount++;
                    } else {
                        throw new \Exception($paymentResult['message'] ?? 'Błąd płatności');
                    }
                    
                    DB::commit();
                } else {
                    // W trybie testowym po prostu wyświetl informacje
                    $this->info("Tryb testowy: Wykonałbym płatność {$amount} {$currency} za pomocą {$paymentMethod}");
                    $successCount++;
                }
                
            } catch (\Exception $e) {
                if (!$dryRun) {
                    DB::rollBack();
                    
                    // Oznacz próbę odnowienia jako nieudaną i powiadom użytkownika
                    if (isset($subscription) && isset($user)) {
                        $subscription->renewal_failed_count = ($subscription->renewal_failed_count ?? 0) + 1;
                        $subscription->last_renewal_attempt = now();
                        $subscription->save();
                        
                        // Powiadom użytkownika o błędzie odnowienia
                        $user->notify(new RenewalFailed($subscription, $e->getMessage()));
                    }
                }
                
                $this->error("Błąd podczas odnawiania subskrypcji ID {$subscription->id}: " . $e->getMessage());
                Log::error("Błąd podczas odnawiania subskrypcji ID {$subscription->id}: " . $e->getMessage());
                $failCount++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        
        if ($dryRun) {
            $this->info("Zakończono testowe odnowienie subskrypcji. Przetworzono: {$count}");
        } else {
            $this->info("Zakończono odnawianie subskrypcji. Pomyślnie odnowiono: {$successCount}, błędy: {$failCount}");
        }
        
        return 0;
    }
    
    /**
     * Oblicza nową datę wygaśnięcia na podstawie bieżącej daty i okresu rozliczeniowego
     */
    private function calculateNewEndDate($currentEndDate, $billingPeriod)
    {
        $endDate = Carbon::parse($currentEndDate);
        
        switch ($billingPeriod) {
            case 'monthly':
                return $endDate->addMonth();
                
            case 'quarterly':
                return $endDate->addMonths(3);
                
            case 'yearly':
                return $endDate->addYear();
                
            default:
                return $endDate->addMonth(); // domyślnie miesięcznie
        }
    }
} 