<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:process-renewals {--force : Wymuś odnowienie wszystkich automatycznych subskrypcji}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Przetwarza odnowienia subskrypcji, które wkrótce wygasną';

    /**
     * @var SubscriptionService
     */
    protected $subscriptionService;

    /**
     * Create a new command instance.
     *
     * @param SubscriptionService $subscriptionService
     * @return void
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Rozpoczynam przetwarzanie odnowień subskrypcji...');
        
        try {
            $renewalWindowDays = config('subscription.renewal_window_days', 7);
            $now = Carbon::now();
            $renewalDate = $now->copy()->addDays($renewalWindowDays);
            
            // Budowanie zapytania
            $query = UserSubscription::where('status', 'active')
                ->where('subscription_type', UserSubscription::TYPE_AUTOMATIC)
                ->where(function ($q) {
                    $q->where('renewal_status', UserSubscription::RENEWAL_ENABLED)
                      ->orWhere('auto_renew', true);
                });
            
            // Jeśli nie wymuszamy odnowienia, dodajemy filtr daty
            if (!$this->option('force')) {
                $query->whereNotNull('end_date')
                      ->whereDate('end_date', '<=', $renewalDate)
                      ->whereDate('end_date', '>', $now);
            }
            
            $subscriptions = $query->get();
            
            $this->info("Znaleziono {$subscriptions->count()} subskrypcji do odnowienia.");
            
            $bar = $this->output->createProgressBar($subscriptions->count());
            $bar->start();
            
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($subscriptions as $subscription) {
                try {
                    $result = $this->subscriptionService->renewSubscription($subscription);
                    
                    if ($result['success']) {
                        $successCount++;
                        $this->info("\nOdnowiono subskrypcję ID: {$subscription->id} dla użytkownika: {$subscription->user->name}");
                    } else {
                        $failureCount++;
                        $this->error("\nNie udało się odnowić subskrypcji ID: {$subscription->id}: {$result['message']}");
                    }
                } catch (\Exception $e) {
                    $failureCount++;
                    $this->error("\nBłąd podczas odnawiania subskrypcji ID: {$subscription->id}: {$e->getMessage()}");
                    Log::error("Błąd podczas odnawiania subskrypcji: {$e->getMessage()}", [
                        'subscription_id' => $subscription->id,
                        'exception' => $e
                    ]);
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            
            $this->info("\nZakończono przetwarzanie odnowień subskrypcji.");
            $this->info("Pomyślnie odnowiono: {$successCount} subskrypcji.");
            $this->info("Nie udało się odnowić: {$failureCount} subskrypcji.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Wystąpił błąd podczas przetwarzania odnowień: {$e->getMessage()}");
            Log::error("Błąd podczas przetwarzania odnowień subskrypcji: {$e->getMessage()}", [
                'exception' => $e
            ]);
            
            return 1;
        }
    }
}
