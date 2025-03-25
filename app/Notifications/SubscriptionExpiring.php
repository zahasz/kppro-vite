<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\UserSubscription;

class SubscriptionExpiring extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Subskrypcja, której dotyczy powiadomienie
     * 
     * @var UserSubscription
     */
    protected $subscription;

    /**
     * Liczba dni do wygaśnięcia
     * 
     * @var int
     */
    protected $daysLeft;

    /**
     * Utwórz nową instancję powiadomienia.
     *
     * @param UserSubscription $subscription
     * @param int $daysLeft
     * @return void
     */
    public function __construct(UserSubscription $subscription, int $daysLeft)
    {
        $this->subscription = $subscription;
        $this->daysLeft = $daysLeft;
    }

    /**
     * Pobierz kanały dostarczania powiadomienia.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Pobierz wiadomość mailową powiadomienia.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user = $notifiable;
        $plan = $this->subscription->plan;
        $expiryDate = $this->subscription->ends_at->format('d.m.Y');
        
        $message = (new MailMessage)
            ->subject('Twoja subskrypcja wkrótce wygaśnie')
            ->greeting('Witaj ' . $user->name . '!');

        if ($this->daysLeft == 1) {
            $message->line('Twoja subskrypcja planu ' . $plan->name . ' wygaśnie jutro, ' . $expiryDate . '.');
        } else {
            $message->line('Twoja subskrypcja planu ' . $plan->name . ' wygaśnie za ' . $this->daysLeft . ' dni, ' . $expiryDate . '.');
        }
        
        if ($this->subscription->auto_renew) {
            $message->line('Subskrypcja zostanie automatycznie odnowiona w dniu wygaśnięcia. Upewnij się, że Twoje dane płatności są aktualne, aby uniknąć przerw w dostępie do usługi.');
        } else {
            $message->line('Po wygaśnięciu subskrypcji Twój dostęp do funkcji planu ' . $plan->name . ' zostanie ograniczony.')
                ->line('Możesz odnowić swoją subskrypcję w dowolnym momencie przed lub po terminie wygaśnięcia.');
        }
        
        $message->action('Zarządzaj subskrypcjami', url('/subscriptions'))
            ->line('Jeśli masz jakiekolwiek pytania, skontaktuj się z naszym zespołem obsługi klienta.')
            ->salutation('Z poważaniem, zespół ' . config('app.name'));
            
        return $message;
    }

    /**
     * Pobierz tablicę danych dla powiadomienia typu database.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $plan = $this->subscription->plan;
        
        return [
            'title' => 'Subskrypcja wkrótce wygaśnie',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan->name,
            'days_left' => $this->daysLeft,
            'ends_at' => $this->subscription->ends_at->toDateTimeString(),
            'auto_renew' => $this->subscription->auto_renew,
        ];
    }
} 