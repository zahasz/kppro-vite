<?php

namespace App\Listeners;

use App\Events\SubscriptionCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogSubscriptionCancellation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Czas (w sekundach), po którym zadanie powinno wygasnąć.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Utwórz nową instancję listenera.
     */
    public function __construct()
    {
        //
    }

    /**
     * Obsłuż zdarzenie.
     */
    public function handle(SubscriptionCancelled $event): void
    {
        $subscription = $event->subscription;
        $user = $subscription->user;
        $plan = $subscription->plan;
        
        Log::channel('subscriptions')->info('Subskrypcja została anulowana', [
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'reason' => $event->reason,
            'auto_cancel' => $event->autoCancel,
            'cancelled_at' => now()->toDateTimeString(),
            'end_date' => $subscription->end_date ? $subscription->end_date->toDateTimeString() : null,
        ]);
        
        // Zapisz również w dzienniku aktywności użytkownika (jeśli istnieje)
        if (method_exists($user, 'logActivity')) {
            $user->logActivity(
                'subscription.cancelled',
                'Anulowano subskrypcję: ' . $plan->name,
                [
                    'subscription_id' => $subscription->id,
                    'plan_id' => $plan->id,
                    'auto_cancel' => $event->autoCancel,
                ]
            );
        }
    }
}
