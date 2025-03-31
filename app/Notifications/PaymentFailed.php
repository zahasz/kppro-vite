<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use App\Models\PaymentSettings;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Subskrypcja, której dotyczy powiadomienie
     *
     * @var UserSubscription
     */
    protected $subscription;

    /**
     * Powód niepowodzenia płatności
     *
     * @var string
     */
    protected $reason;

    /**
     * Tworzenie nowej instancji powiadomienia.
     *
     * @param UserSubscription $subscription
     * @param string $reason
     * @return void
     */
    public function __construct(UserSubscription $subscription, $reason = '')
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
        $settings = PaymentSettings::getActive();
        $gracePeriod = $settings->grace_period_days ?? 3;
        $retryInterval = $settings->payment_retry_interval ?? 3;
        $retryDate = Carbon::now()->addDays($retryInterval)->format('d.m.Y');
        
        $mail = (new MailMessage)
            ->subject('Płatność za subskrypcję nie powiodła się')
            ->greeting('Witaj ' . $notifiable->name . '!')
            ->line('Niestety, nie udało nam się pobrać płatności za Twoją subskrypcję **' . $plan->name . '**.');
            
        if ($this->reason) {
            $mail->line('Powód niepowodzenia: **' . $this->reason . '**');
        }
        
        if ($gracePeriod > 0) {
            $graceEndDate = Carbon::now()->addDays($gracePeriod)->format('d.m.Y');
            $mail->line('Twoja subskrypcja pozostanie aktywna do **' . $graceEndDate . '** (okres karencji).');
        }
        
        if ($settings->auto_retry_failed_payments) {
            $mail->line('Automatycznie ponowimy próbę płatności dnia **' . $retryDate . '**.');
        }
        
        $mail->line('Aby uniknąć przerwania usługi, prosimy o sprawdzenie i aktualizację informacji o płatności.')
             ->action('Zarządzaj subskrypcją', url('/account/subscriptions'))
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
        $settings = PaymentSettings::getActive();
        $retryInterval = $settings->payment_retry_interval ?? 3;
        $nextRetry = Carbon::now()->addDays($retryInterval);
        
        return [
            'title' => 'Płatność nie powiodła się',
            'message' => 'Nie udało się pobrać płatności za subskrypcję ' . $plan->name,
            'subscription_id' => $this->subscription->id,
            'reason' => $this->reason,
            'next_retry' => $nextRetry->format('Y-m-d'),
            'action_url' => '/account/subscriptions',
        ];
    }
} 