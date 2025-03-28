<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupExpiredSubscriptions extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:cleanup-expired {--days=7 : Liczba dni po wygaśnięciu}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Oznacza subskrypcje jako wygasłe, jeśli minęła ich data końcowa';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        $this->info("Rozpoczynam czyszczenie wygasłych subskrypcji...");
        
        // Znajdź aktywne subskrypcje, które wygasły określoną liczbę dni temu lub wcześniej
        $expirationDate = Carbon::now()->subDays($days)->format('Y-m-d');
        
        $expiredSubscriptions = UserSubscription::with('user')
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', $expirationDate)
            ->where('auto_renew', false) // Tylko te, które nie mają włączonego auto-odnowienia
            ->get();
            
        $count = $expiredSubscriptions->count();
        
        if ($count === 0) {
            $this->info("Nie znaleziono wygasłych subskrypcji do przetworzenia.");
            return 0;
        }
        
        $this->info("Znaleziono {$count} wygasłych subskrypcji do przetworzenia.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $processedCount = 0;
        
        foreach ($expiredSubscriptions as $subscription) {
            $user = $subscription->user;
            
            if (!$user) {
                $this->error("Nie znaleziono użytkownika dla subskrypcji #{$subscription->id}");
                $bar->advance();
                continue;
            }
            
            $this->line("");
            $this->line("Subskrypcja #{$subscription->id} dla użytkownika {$user->email} wygasła {$subscription->end_date}");
            
            // Oznacz subskrypcję jako wygasłą
            $subscription->status = 'expired';
            $subscription->save();
            
            // Dodaj wpis do dziennika systemowego
            Log::info("Subskrypcja #{$subscription->id} dla użytkownika {$user->email} została oznaczona jako wygasła.");
            
            // Tutaj można dodać logikę wysyłki e-maila do użytkownika
            // Mail::to($user->email)->send(new \App\Mail\SubscriptionExpired($user, $subscription));
            
            $processedCount++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        $this->info("Zakończono czyszczenie wygasłych subskrypcji.");
        $this->info("Oznaczono {$processedCount} subskrypcji jako wygasłe.");
        
        return 0;
    }
}
