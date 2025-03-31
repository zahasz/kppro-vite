<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Subskrypcja, której dotyczy powiadomienie
     * 
     * @var UserSubscription
     */
    protected $subscription;

    /**
     * Utwórz nową instancję powiadomienia.
     *
     * @param UserSubscription $subscription
     * @return void
     */
    public function __construct(UserSubscription $subscription)
    {
        $this->subscription = $subscription;
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
        $endDate = $this->subscription->end_date 
            ? $this->subscription->end_date->format('d.m.Y') 
            : 'bezterminowo';
        
        return (new MailMessage)
            ->subject('Subskrypcja została aktywowana')
            ->greeting('Witaj ' . $user->name . '!')
            ->line('Twoja subskrypcja planu ' . $plan->name . ' została pomyślnie aktywowana.')
            ->line('Subskrypcja jest ważna do: ' . $endDate)
            ->line('Dziękujemy za skorzystanie z naszych usług.')
            ->action('Zarządzaj subskrypcjami', url('/subscription'))
            ->salutation('Z poważaniem, zespół ' . config('app.name'));
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
            'title' => 'Subskrypcja aktywowana',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan->name,
            'end_date' => $this->subscription->end_date?->toDateTimeString(),
        ];
    }
} 