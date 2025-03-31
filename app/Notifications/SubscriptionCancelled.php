<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

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
     * Przyczyna anulowania
     *
     * @var string|null
     */
    protected $reason;

    /**
     * Tworzenie nowej instancji powiadomienia.
     *
     * @param UserSubscription $subscription
     * @param string|null $reason
     * @return void
     */
    public function __construct(UserSubscription $subscription, $reason = null)
    {
        $this->subscription = $subscription;
        $this->reason = $reason;
    }

    /**
     * Pobierz kanały dostarczania powiadomienia.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Pobierz wiadomość e-mail dla powiadomienia.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $plan = $this->subscription->plan;
        $endDate = $this->subscription->end_date
            ? Carbon::parse($this->subscription->end_date)->format('d.m.Y')
            : Carbon::now()->format('d.m.Y');
        
        $mail = (new MailMessage)
            ->subject('Subskrypcja została anulowana')
            ->greeting('Witaj ' . $notifiable->name . '!')
            ->line('Twoja subskrypcja **' . $plan->name . '** została anulowana.');
            
        if ($this->reason) {
            $mail->line('Przyczyna anulowania: **' . $this->reason . '**');
        }
        
        if ($this->subscription->end_date && $this->subscription->end_date->isFuture()) {
            $mail->line('Twoja subskrypcja będzie aktywna do **' . $endDate . '**.');
            $mail->line('Po tym czasie dostęp do usług związanych z tą subskrypcją zostanie ograniczony.');
        } else {
            $mail->line('Twój dostęp do usług związanych z tą subskrypcją został ograniczony.');
        }
        
        $mail->line('Jeśli to anulowanie nastąpiło przez pomyłkę lub chcesz odnowić subskrypcję, możesz to zrobić w każdej chwili.')
             ->action('Zarządzaj subskrypcjami', url('/account/subscriptions'))
             ->line('Dziękujemy za korzystanie z naszych usług!');
        
        return $mail;
    }

    /**
     * Pobierz tablicę powiadomienia dla bazy danych.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $plan = $this->subscription->plan;
        
        return [
            'title' => 'Subskrypcja anulowana',
            'message' => 'Twoja subskrypcja ' . $plan->name . ' została anulowana.',
            'subscription_id' => $this->subscription->id,
            'plan_id' => $plan->id,
            'reason' => $this->reason,
            'cancelled_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'end_date' => $this->subscription->end_date ? $this->subscription->end_date->format('Y-m-d') : null,
            'action_url' => '/account/subscriptions',
        ];
    }
} 