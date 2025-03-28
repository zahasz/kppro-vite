<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Subskrypcja, której dotyczy powiadomienie
     * 
     * @var UserSubscription
     */
    protected $subscription;

    /**
     * Czy subskrypcja została anulowana przez administratora
     * 
     * @var bool
     */
    protected $byAdmin;

    /**
     * Powód anulowania (opcjonalny)
     * 
     * @var string|null
     */
    protected $reason;

    /**
     * Utwórz nową instancję powiadomienia.
     *
     * @param UserSubscription $subscription
     * @param bool $byAdmin
     * @param string|null $reason
     * @return void
     */
    public function __construct(UserSubscription $subscription, bool $byAdmin = false, string $reason = null)
    {
        $this->subscription = $subscription;
        $this->byAdmin = $byAdmin;
        $this->reason = $reason;
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
        $endDate = $this->subscription->ends_at ? $this->subscription->ends_at->format('d.m.Y') : 'natychmiast';
        
        $message = (new MailMessage)
            ->subject('Anulowanie subskrypcji')
            ->greeting('Witaj ' . $user->name . '!');
            
        if ($this->byAdmin) {
            $message->line('Twoja subskrypcja planu ' . $plan->name . ' została anulowana przez administratora systemu.');
            
            if ($this->reason) {
                $message->line('Powód: ' . $this->reason);
            }
        } else {
            $message->line('Potwierdzamy anulowanie Twojej subskrypcji planu ' . $plan->name . '.');
        }
        
        $message->line('Dostęp do funkcji związanych z tym planem zakończy się ' . $endDate . '.');
        
        if (!$this->byAdmin) {
            $message->line('Jeśli anulowanie było pomyłką lub zmieniłeś zdanie, możesz ponownie aktywować swoją subskrypcję przed upływem terminu ważności.')
                ->action('Zarządzaj subskrypcjami', url('/subscriptions'));
        }
        
        $message->line('Jeśli masz jakiekolwiek pytania, skontaktuj się z naszym zespołem obsługi klienta.')
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
            'title' => 'Anulowanie subskrypcji',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan->name,
            'by_admin' => $this->byAdmin,
            'reason' => $this->reason,
            'ends_at' => $this->subscription->ends_at?->toDateTimeString(),
        ];
    }
} 