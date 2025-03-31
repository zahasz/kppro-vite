<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\PaymentSettings;
use App\Services\SubscriptionService;
use App\Services\PaymentGatewayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RetryFailedPaymentsCommand extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:retry-payments 
                            {--dry-run : Tryb testowy bez wykonywania faktycznych płatności i zmian}
                            {--limit=30 : Maksymalna liczba płatności do ponowienia}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Ponowne próby pobierania płatności, które wcześniej się nie powiodły';

    /**
     * Usługa subskrypcji
     *
     * @var SubscriptionService
     */
    protected $subscriptionService;

    /**
     * Usługa bramek płatności
     *
     * @var PaymentGatewayService
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
        $limit = (int) $this->option('limit');
        
        // Pobierz ustawienia płatności
        $paymentSettings = PaymentSettings::getActive();
        
        if (!$paymentSettings->auto_retry_failed_payments) {
            $this->warn("Automatyczne ponowne próby płatności są wyłączone w ustawieniach systemu.");
            
            if (!$this->confirm('Czy mimo to chcesz kontynuować?', false)) {
                return 0;
            }
        }
        
        if ($dryRun) {
            $this->info("Uruchomiono w trybie testowym (dry-run) - płatności NIE będą wykonywane.");
        }
        
        $this->info("Rozpoczynam proces ponownych prób płatności...");
        
        // Znajdź wszystkie subskrypcje wymagające ponownej próby płatności
        $today = Carbon::now()->format('Y-m-d');
        
        $subscriptions = UserSubscription::where(function($query) {
                // Subskrypcje w okresie karencji lub w statusie oczekiwania na ponowną próbę
                $query->where('status', 'active')
                      ->whereNotNull('grace_period_ends_at')
                      ->orWhere('status', 'payment_retry');
            })
            ->where(function($query) use ($today) {
                // Pobierz tylko te, których termin ponownej próby jest dzisiaj lub wcześniej
                $query->whereDate('next_payment_retry', '<=', $today)
                      ->orWhereNull('next_payment_retry');
            })
            ->where('failed_payment_count', '>', 0) // Musi mieć co najmniej jedną nieudaną płatność
            ->where('failed_payment_count', '<', $paymentSettings->payment_retry_attempts) // Nie przekroczono maksymalnej liczby prób
            ->limit($limit)
            ->get();
            
        $count = $subscriptions->count();
        
        if ($count === 0) {
            $this->info("Nie znaleziono subskrypcji wymagających ponownej próby płatności.");
            return 0;
        }
        
        $this->info("Znaleziono {$count} subskrypcji do ponownego przetworzenia płatności.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($subscriptions as $subscription) {
            if (!$dryRun) {
                DB::beginTransaction();
            }
            
            try {
                $user = $subscription->user;
                $plan = $subscription->plan;
                
                if (!$user || !$plan) {
                    throw new \Exception("Nie znaleziono użytkownika lub planu subskrypcji.");
                }
                
                $this->line("");
                $this->info("Przetwarzanie ponownej próby płatności dla subskrypcji ID {$subscription->id} ({$plan->name}) użytkownika {$user->email}");
                
                if (!$dryRun) {
                    // Przygotuj dane płatności
                    $paymentMethod = $subscription->payment_method;
                    $paymentDetails = $subscription->payment_details;
                    $amount = $plan->price;
                    $currency = $plan->currency ?? 'PLN';
                    $description = "Ponowna próba płatności za subskrypcję {$plan->name}";
                    
                    // Wykonaj ponowną próbę płatności
                    $result = $this->paymentGatewayService->processPayment(
                        $user,
                        $amount,
                        $currency,
                        $paymentMethod,
                        $paymentDetails,
                        $description,
                        [
                            'subscription_id' => $subscription->id,
                            'is_retry' => true,
                            'retry_attempt' => $subscription->failed_payment_count + 1
                        ]
                    );
                    
                    if ($result['success']) {
                        // Aktualizuj subskrypcję
                        $subscription->status = 'active';
                        $subscription->failed_payment_count = 0;
                        $subscription->next_payment_retry = null;
                        $subscription->grace_period_ends_at = null;
                        $subscription->last_payment_date = now();
                        $subscription->save();
                        
                        // Powiadom użytkownika o udanej płatności
                        $user->notify(new \App\Notifications\PaymentSuccessful($subscription, $amount, $currency));
                        
                        $this->info("Pomyślnie przetworzono ponowną płatność dla subskrypcji ID {$subscription->id}");
                        $successCount++;
                        
                        DB::commit();
                    } else {
                        // Obsłuż nieudaną płatność
                        $this->subscriptionService->handleFailedPayment(
                            $subscription, 
                            $result['message'] ?? 'Nieudana ponowna próba płatności'
                        );
                        
                        $this->warn("Ponowna próba płatności dla subskrypcji ID {$subscription->id} nie powiodła się: {$result['message']}");
                        $failCount++;
                        
                        DB::commit();
                    }
                } else {
                    // W trybie testowym tylko wyświetl informację
                    $this->info("Tryb testowy: Wykonałbym ponowną próbę płatności za subskrypcję ID {$subscription->id}");
                    $successCount++;
                }
            } catch (\Exception $e) {
                if (!$dryRun) {
                    DB::rollBack();
                }
                
                $this->error("Błąd podczas przetwarzania subskrypcji ID {$subscription->id}: " . $e->getMessage());
                Log::error("Błąd podczas przetwarzania ponownej próby płatności dla subskrypcji ID {$subscription->id}: " . $e->getMessage(), [
                    'exception' => $e,
                    'subscription_id' => $subscription->id
                ]);
                
                $failCount++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        
        if ($dryRun) {
            $this->info("Zakończono testowe przetwarzanie ponownych prób płatności. Przetworzono: {$count}");
        } else {
            $this->info("Zakończono przetwarzanie ponownych prób płatności. Sukces: {$successCount}, błędy: {$failCount}");
        }
        
        return 0;
    }
} 