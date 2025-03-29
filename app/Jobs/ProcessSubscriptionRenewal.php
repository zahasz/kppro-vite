<?php

namespace App\Jobs;

use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionRenewal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maksymalna liczba prób wykonania zadania.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Czas w sekundach przed ponowną próbą.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * ID subskrypcji do odnowienia.
     *
     * @var int
     */
    protected $subscriptionId;

    /**
     * Create a new job instance.
     *
     * @param int $subscriptionId
     * @return void
     */
    public function __construct(int $subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * Execute the job.
     *
     * @param SubscriptionService $subscriptionService
     * @return void
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        try {
            $subscription = UserSubscription::find($this->subscriptionId);
            
            if (!$subscription) {
                Log::warning("Nie znaleziono subskrypcji o ID: {$this->subscriptionId}");
                return;
            }
            
            if (!$subscription->canBeRenewed()) {
                Log::info("Subskrypcja ID: {$this->subscriptionId} nie może być odnowiona w tym momencie");
                return;
            }
            
            $result = $subscriptionService->renewSubscription($subscription);
            
            if ($result['success']) {
                Log::info("Pomyślnie odnowiono subskrypcję ID: {$this->subscriptionId}");
            } else {
                Log::warning("Nie udało się odnowić subskrypcji ID: {$this->subscriptionId}: {$result['message']}");
                
                // W przypadku niepowodzenia może być potrzebna dodatkowa logika np. oznaczenie subskrypcji
                // jak wymagającej ręcznej interwencji, powiadomienie administratora, etc.
            }
        } catch (\Exception $e) {
            Log::error("Błąd podczas przetwarzania odnowienia subskrypcji ID: {$this->subscriptionId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Rzuć wyjątek ponownie, aby zadanie mogło być ponownie wykonane zgodnie z mechanizmem ponawiania
            throw $e;
        }
    }

    /**
     * Obsługa nieudanego zadania.
     *
     * @param \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error("Zadanie odnowienia subskrypcji ID: {$this->subscriptionId} nie powiodło się", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Można tutaj dodać powiadomienia lub inne działania
        // np. wysłanie maila do administratora lub oznaczenie subskrypcji jako wymagającej ręcznej interwencji
    }
} 