<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:process-renewals {--limit=20 : Maksymalna liczba subskrypcji do przetworzenia}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Przetwarza automatyczne odnowienia subskrypcji użytkowników';

    /**
     * Maksymalna liczba prób odnowienia subskrypcji.
     */
    protected $maxRenewalAttempts = 3;

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        
        $this->info("Rozpoczynam przetwarzanie automatycznych odnowień subskrypcji (limit: {$limit})...");
        
        // Znajdź subskrypcje, które wygasają dzisiaj lub wygasły wczoraj, z włączonym auto-odnowieniem
        $now = Carbon::now();
        $yesterday = Carbon::yesterday();
        
        $expiredSubscriptions = UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->where(function($query) use ($now, $yesterday) {
                $query->whereDate('end_date', $now->format('Y-m-d'))
                    ->orWhereDate('end_date', $yesterday->format('Y-m-d'));
            })
            ->where(function($query) {
                // Subskrypcje, które nie miały żadnych prób odnowienia lub miały mniej niż maksymalna liczba prób
                $query->whereNull('last_renewal_attempt')
                    ->orWhere('renewal_attempts', '<', $this->maxRenewalAttempts);
            })
            ->orderBy('end_date')
            ->limit($limit)
            ->get();
        
        $count = $expiredSubscriptions->count();
        
        if ($count === 0) {
            $this->info("Nie znaleziono subskrypcji do odnowienia.");
            return 0;
        }
        
        $this->info("Znaleziono {$count} subskrypcji do odnowienia.");
        
        $renewedCount = 0;
        $failedCount = 0;
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        foreach ($expiredSubscriptions as $subscription) {
            $user = $subscription->user;
            $plan = $subscription->subscriptionPlan;
            
            if (!$user || !$plan) {
                $this->error("Brak użytkownika lub planu dla subskrypcji #{$subscription->id}");
                $failedCount++;
                $bar->advance();
                continue;
            }
            
            // Zwiększ licznik prób odnowienia
            $subscription->renewal_attempts += 1;
            $subscription->last_renewal_attempt = Carbon::now();
            $subscription->save();
            
            $this->line("");
            $this->line("Przetwarzanie subskrypcji #{$subscription->id} dla {$user->email} (plan: {$plan->name})");
            
            // W rzeczywistym systemie tutaj byłaby integracja z bramką płatności
            // Na potrzeby tego przykładu symulujemy udaną płatność
            
            DB::beginTransaction();
            
            try {
                // Oblicz nową datę zakończenia subskrypcji
                $newEndDate = null;
                
                switch ($plan->billing_period) {
                    case 'monthly':
                        $newEndDate = Carbon::parse($subscription->end_date)->addMonth();
                        break;
                    case 'quarterly':
                        $newEndDate = Carbon::parse($subscription->end_date)->addMonths(3);
                        break;
                    case 'yearly':
                        $newEndDate = Carbon::parse($subscription->end_date)->addYear();
                        break;
                    case 'lifetime':
                        // Dla bezterminowych nie ustawiamy daty końca
                        $newEndDate = null;
                        break;
                }
                
                // Utwórz płatność dla odnowionej subskrypcji
                $transactionId = 'TXN' . time() . Str::random(6);
                $invoiceNumber = 'INV' . date('Ymd') . Str::random(4);
                
                // W rzeczywistym systemie tutaj byłaby integracja z bramką płatności
                // Na potrzeby tego przykładu symulujemy udaną płatność
                
                $paymentStatus = 'paid'; // W rzeczywistości to by zależało od odpowiedzi bramki płatności
                
                if ($paymentStatus === 'paid') {
                    // Aktualizuj subskrypcję
                    $subscription->update([
                        'end_date' => $newEndDate,
                        'status' => 'active',
                    ]);
                    
                    // Utwórz wpis płatności
                    SubscriptionPayment::create([
                        'transaction_id' => $transactionId,
                        'user_id' => $user->id,
                        'user_subscription_id' => $subscription->id,
                        'amount' => $plan->price,
                        'status' => 'paid',
                        'payment_method' => $subscription->payment_method,
                        'payment_details' => 'Automatyczne odnowienie subskrypcji',
                        'invoice_number' => $invoiceNumber,
                        'invoice_date' => Carbon::now(),
                        'notes' => 'Automatyczne odnowienie przez system',
                    ]);
                    
                    $this->info("Subskrypcja #{$subscription->id} została pomyślnie odnowiona do {$newEndDate}");
                    Log::info("Subskrypcja #{$subscription->id} dla użytkownika {$user->email} została odnowiona do {$newEndDate}");
                    
                    $renewedCount++;
                } else {
                    // Jeśli płatność się nie powiodła, oznaczamy subskrypcję jako 'pending'
                    $subscription->update([
                        'status' => 'pending',
                    ]);
                    
                    $this->error("Płatność za odnowienie subskrypcji #{$subscription->id} nie powiodła się");
                    Log::error("Płatność za odnowienie subskrypcji #{$subscription->id} dla użytkownika {$user->email} nie powiodła się");
                    
                    $failedCount++;
                }
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Błąd podczas przetwarzania odnowienia: " . $e->getMessage());
                Log::error("Błąd odnowienia subskrypcji #{$subscription->id}: " . $e->getMessage());
                $failedCount++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        $this->info("Zakończono przetwarzanie odnowień subskrypcji.");
        $this->info("Odnowiono: {$renewedCount}, Niepowodzenia: {$failedCount}");
        
        return 0;
    }
}
