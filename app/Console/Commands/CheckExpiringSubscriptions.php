<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckExpiringSubscriptions extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiring {--days=7 : Liczba dni przed wygaśnięciem do sprawdzenia} {--send-emails : Czy wysyłać e-maile z powiadomieniami}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Sprawdza wygasające subskrypcje i wysyła powiadomienia';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $sendEmails = $this->option('send-emails');
        
        $this->info("Sprawdzam subskrypcje wygasające za {$days} dni...");
        
        $expirationDate = Carbon::now()->addDays($days)->format('Y-m-d');
        
        // Znajdź subskrypcje, które wygasają w określonej dacie
        $expiringSubscriptions = UserSubscription::with('user', 'subscriptionPlan')
            ->where('status', 'active')
            ->whereDate('end_date', $expirationDate)
            ->get();
            
        $count = $expiringSubscriptions->count();
        
        if ($count === 0) {
            $this->info("Nie znaleziono subskrypcji wygasających za {$days} dni.");
            return 0;
        }
        
        $this->info("Znaleziono {$count} subskrypcji wygasających za {$days} dni.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        foreach ($expiringSubscriptions as $subscription) {
            $user = $subscription->user;
            $plan = $subscription->subscriptionPlan;
            
            if (!$user) {
                $this->error("Nie znaleziono użytkownika dla subskrypcji #{$subscription->id}");
                continue;
            }
            
            $this->line("");
            $this->line("Subskrypcja: #{$subscription->id} - Plan: {$plan->name} - Użytkownik: {$user->email}");
            
            // Dodaj wpis do dziennika systemowego
            Log::info("Subskrypcja #{$subscription->id} dla użytkownika {$user->email} wygasa za {$days} dni.");
            
            // Wysyłka e-maila, jeśli opcja włączona
            if ($sendEmails) {
                $this->line("Wysyłanie powiadomienia do użytkownika {$user->email}");
                
                try {
                    // Tutaj kod do wysyłania e-maila
                    // Mail::to($user->email)->send(new \App\Mail\SubscriptionExpiring($user, $subscription, $days));
                    
                    // Zakomentowano rzeczywistą wysyłkę e-maila, aby uniknąć wysyłania podczas testów
                    // Zamiast tego, logujemy informację że e-mail zostałby wysłany
                    Log::info("E-mail powiadamiający o wygaśnięciu subskrypcji zostałby wysłany do: {$user->email}");
                    
                    // Aktualizuj flagę w subskrypcji, że powiadomienie zostało wysłane
                    $subscription->update([
                        'notified_at' => Carbon::now(),
                        'last_notification_type' => "expiring_{$days}_days",
                    ]);
                    
                } catch (\Exception $e) {
                    $this->error("Błąd podczas wysyłania e-maila do {$user->email}: " . $e->getMessage());
                    Log::error("Błąd wysyłki powiadomienia o wygasającej subskrypcji: " . $e->getMessage());
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        $this->info("Zakończono sprawdzanie subskrypcji wygasających za {$days} dni.");
        
        return 0;
    }
}
