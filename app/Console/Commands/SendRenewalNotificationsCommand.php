<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\PaymentSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Notifications\SubscriptionRenewalReminder;

class SendRenewalNotificationsCommand extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:notify-renewals 
                            {--dry-run : Tryb testowy bez wysyłania powiadomień}
                            {--days-before= : Liczba dni przed wygaśnięciem, kiedy wysyłane jest powiadomienie}
                            {--limit=50 : Maksymalna liczba powiadomień do wysłania}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Wysyła powiadomienia o zbliżającym się odnowieniu subskrypcji';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        // Pobierz ustawienia systemowe
        $paymentSettings = PaymentSettings::getActive();
        
        // Użyj parametru z linii komend lub wartości z ustawień
        $daysBefore = (int) ($this->option('days-before') ?: $paymentSettings->renewal_notification_days ?? 7);
        $limit = (int) $this->option('limit');
        
        // Sprawdź, czy powiadomienia są włączone w ustawieniach
        if (!$paymentSettings->renewal_notifications && !$this->option('days-before')) {
            $this->warn("Powiadomienia o odnowieniu subskrypcji są wyłączone w ustawieniach systemu.");
            
            if (!$this->confirm('Czy mimo to chcesz kontynuować?', false)) {
                return 0;
            }
        }
        
        if ($dryRun) {
            $this->info("Uruchomiono w trybie testowym (dry-run) - powiadomienia NIE będą wysyłane.");
        }
        
        $this->info("Rozpoczynam proces wysyłania powiadomień o odnowieniu subskrypcji ({$daysBefore} dni przed wygaśnięciem)...");
        
        // Data, według której sprawdzamy subskrypcje
        $notifyDate = Carbon::now()->addDays($daysBefore);
        
        // Pobierz subskrypcje do powiadomienia
        $subscriptionsToNotify = UserSubscription::where('status', 'active')
            ->where(function ($query) {
                $query->where('auto_renew', true)
                      ->orWhere('renewal_status', UserSubscription::RENEWAL_ENABLED);
            })
            ->whereDate('end_date', '=', $notifyDate->toDateString())
            ->whereNotNull('end_date')
            ->whereNull('renewal_notification_sent')  // nie wysłano jeszcze powiadomienia
            ->limit($limit)
            ->get();
            
        $count = $subscriptionsToNotify->count();
        
        if ($count === 0) {
            $this->info("Nie znaleziono subskrypcji wymagających powiadomień o odnowieniu.");
            return 0;
        }
        
        $this->info("Znaleziono {$count} subskrypcji do powiadomienia.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($subscriptionsToNotify as $subscription) {
            try {
                $user = $subscription->user;
                $plan = $subscription->plan;
                
                if (!$user || !$plan) {
                    throw new \Exception("Nie znaleziono użytkownika lub planu subskrypcji.");
                }
                
                if (!$dryRun) {
                    // Wysyłamy powiadomienie do użytkownika
                    $user->notify(new SubscriptionRenewalReminder($subscription));
                    
                    // Oznaczamy, że powiadomienie zostało wysłane
                    $subscription->renewal_notification_sent = now();
                    $subscription->save();
                    
                    $successCount++;
                } else {
                    $this->line("");
                    $this->info("Tryb testowy: Wysłałbym powiadomienie do {$user->email} o odnowieniu subskrypcji {$plan->name}");
                    $successCount++;
                }
                
            } catch (\Exception $e) {
                $this->error("Błąd podczas wysyłania powiadomienia dla subskrypcji ID {$subscription->id}: " . $e->getMessage());
                Log::error("Błąd podczas wysyłania powiadomienia dla subskrypcji ID {$subscription->id}: " . $e->getMessage());
                $failCount++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        
        if ($dryRun) {
            $this->info("Zakończono testowe wysyłanie powiadomień. Przetworzono: {$count}");
        } else {
            $this->info("Zakończono wysyłanie powiadomień. Pomyślnie wysłano: {$successCount}, błędy: {$failCount}");
        }
        
        return 0;
    }
} 