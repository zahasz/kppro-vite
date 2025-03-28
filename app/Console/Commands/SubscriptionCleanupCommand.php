<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\SubscriptionExpiring;
use App\Notifications\SubscriptionExpired;

class SubscriptionCleanupCommand extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:cleanup 
                            {--dry-run : Tryb testowy bez zapisywania zmian}
                            {--notify-expired : Powiadamia użytkowników o wygaśnięciu subskrypcji}
                            {--notify-expiring : Powiadamia użytkowników o zbliżającym się wygaśnięciu subskrypcji}
                            {--days-before=7 : Liczba dni przed wygaśnięciem do wysłania powiadomienia}
                            {--limit=100 : Maksymalna liczba rekordów do przetworzenia}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Zarządza nieaktywnymi subskrypcjami, oznacza wygasłe i wysyła powiadomienia o kończących się subskrypcjach';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $notifyExpired = $this->option('notify-expired');
        $notifyExpiring = $this->option('notify-expiring');
        $daysBefore = (int) $this->option('days-before');
        $limit = (int) $this->option('limit');
        
        if ($dryRun) {
            $this->info("Uruchomiono w trybie testowym (dry-run) - zmiany NIE będą zapisywane.");
        }
        
        $now = Carbon::now();
        $expiringDate = Carbon::now()->addDays($daysBefore);
        
        // Zadanie 1: Oznaczanie wygasłych subskrypcji
        $this->info("Sprawdzam wygasłe subskrypcje...");
        
        $expiredQuery = UserSubscription::where('status', 'active')
            ->where('end_date', '<', $now)
            ->where('end_date', '!=', null)
            ->limit($limit);
            
        $expiredCount = $expiredQuery->count();
        
        if ($expiredCount > 0) {
            $this->info("Znaleziono {$expiredCount} wygasłych subskrypcji.");
            
            $expiredSubscriptions = $expiredQuery->get();
            $bar = $this->output->createProgressBar($expiredCount);
            $bar->start();
            
            $processedCount = 0;
            $errorCount = 0;
            
            foreach ($expiredSubscriptions as $subscription) {
                if (!$dryRun) {
                    DB::beginTransaction();
                }
                
                try {
                    if (!$dryRun) {
                        $subscription->status = 'expired';
                        $subscription->save();
                        
                        // Powiadomienie o wygaśnięciu subskrypcji
                        if ($notifyExpired && $subscription->user) {
                            $subscription->user->notify(new SubscriptionExpired($subscription));
                        }
                    }
                    
                    $this->line("");
                    $this->info("Oznaczono subskrypcję ID {$subscription->id} użytkownika {$subscription->user->email} jako wygasłą.");
                    
                    if (!$dryRun) {
                        DB::commit();
                    }
                    
                    $processedCount++;
                    
                } catch (\Exception $e) {
                    if (!$dryRun) {
                        DB::rollBack();
                    }
                    
                    $this->error("Błąd podczas przetwarzania subskrypcji ID {$subscription->id}: " . $e->getMessage());
                    Log::error("Błąd podczas przetwarzania subskrypcji ID {$subscription->id}: " . $e->getMessage());
                    $errorCount++;
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->line("");
            
            if ($dryRun) {
                $this->info("Znaleziono {$expiredCount} wygasłych subskrypcji (tryb testowy).");
            } else {
                $this->info("Oznaczono {$processedCount} subskrypcji jako wygasłe. Błędy: {$errorCount}");
            }
            
        } else {
            $this->info("Nie znaleziono wygasłych subskrypcji.");
        }
        
        // Zadanie 2: Powiadamianie o zbliżającym się wygaśnięciu subskrypcji
        if ($notifyExpiring) {
            $this->info("Sprawdzam subskrypcje zbliżające się do wygaśnięcia...");
            
            $expiringQuery = UserSubscription::where('status', 'active')
                ->whereDate('end_date', '=', $expiringDate->toDateString())
                ->where('end_date', '!=', null)
                ->limit($limit);
                
            $expiringCount = $expiringQuery->count();
            
            if ($expiringCount > 0) {
                $this->info("Znaleziono {$expiringCount} subskrypcji, które wygasną za {$daysBefore} dni.");
                
                $expiringSubscriptions = $expiringQuery->get();
                $bar = $this->output->createProgressBar($expiringCount);
                $bar->start();
                
                $notifiedCount = 0;
                $errorCount = 0;
                
                foreach ($expiringSubscriptions as $subscription) {
                    try {
                        if (!$dryRun && $subscription->user) {
                            $subscription->user->notify(new SubscriptionExpiring($subscription, $daysBefore));
                            
                            // Zapisujemy informację, że powiadomienie zostało wysłane
                            $subscription->last_reminder_sent_at = now();
                            $subscription->save();
                        }
                        
                        $this->line("");
                        $this->info("Wysłano powiadomienie o zbliżającym się wygaśnięciu subskrypcji dla użytkownika {$subscription->user->email}.");
                        
                        $notifiedCount++;
                        
                    } catch (\Exception $e) {
                        $this->error("Błąd podczas wysyłania powiadomienia dla subskrypcji ID {$subscription->id}: " . $e->getMessage());
                        Log::error("Błąd podczas wysyłania powiadomienia dla subskrypcji ID {$subscription->id}: " . $e->getMessage());
                        $errorCount++;
                    }
                    
                    $bar->advance();
                }
                
                $bar->finish();
                $this->line("");
                
                if ($dryRun) {
                    $this->info("Znaleziono {$expiringCount} subskrypcji do powiadomienia (tryb testowy).");
                } else {
                    $this->info("Wysłano {$notifiedCount} powiadomień o zbliżającym się wygaśnięciu. Błędy: {$errorCount}");
                }
                
            } else {
                $this->info("Nie znaleziono subskrypcji zbliżających się do wygaśnięcia w ciągu {$daysBefore} dni.");
            }
        }
        
        // Zadanie 3: Czyszczenie usuniętych subskrypcji
        $this->info("Sprawdzam usunięte subskrypcje...");
        
        $deletedQuery = UserSubscription::onlyTrashed()
            ->where('deleted_at', '<', now()->subMonths(6))
            ->limit($limit);
            
        $deletedCount = $deletedQuery->count();
        
        if ($deletedCount > 0) {
            $this->info("Znaleziono {$deletedCount} subskrypcji do trwałego usunięcia (starsze niż 6 miesięcy).");
            
            if (!$dryRun) {
                $deletedQuery->forceDelete();
                $this->info("Trwale usunięto {$deletedCount} subskrypcji.");
            } else {
                $this->info("Tryb testowy - nie usunięto żadnych danych.");
            }
        } else {
            $this->info("Nie znaleziono usuniętych subskrypcji starszych niż 6 miesięcy.");
        }
        
        $this->info("Zakończono zadanie czyszczenia subskrypcji.");
        
        return 0;
    }
} 