<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class PaymentSuccessful extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Subskrypcja, której dotyczy powiadomienie
     *
     * @var UserSubscription
     */
    protected $subscription;

    /**
     * Kwota płatności
     *
     * @var float
     */
    protected $amount;

    /**
     * Waluta płatności
     *
     * @var string
     */
    protected $currency;

    /**
     * Czy jest to ponowna udana próba płatności
     *
     * @var bool
     */
    protected $isRetry;

    /**
     * Tworzenie nowej instancji powiadomienia.
     *
     * @param UserSubscription $subscription
     * @param float $amount
     * @param string $currency
     * @param bool $isRetry
     * @return void
     */
    public function __construct(UserSubscription $subscription, float $amount, string $currency = 'PLN', bool $isRetry = false)
    {
        $this->subscription = $subscription;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->isRetry = $isRetry;
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
            : null;
        
        $mail = (new MailMessage)
            ->subject($this->isRetry 
                ? 'Płatność za subskrypcję została pomyślnie przetworzona' 
                : 'Potwierdzenie płatności za subskrypcję')
            ->greeting('Witaj ' . $notifiable->name . '!');
            
        if ($this->isRetry) {
            $mail->line('Informujemy, że ponowna próba pobierania płatności za Twoją subskrypcję **' . $plan->name . '** zakończyła się sukcesem.');
        } else {
            $mail->line('Dziękujemy za płatność za subskrypcję **' . $plan->name . '**.');
        }
        
        $mail->line('Szczegóły płatności:')
             ->line('- Kwota: **' . number_format($this->amount, 2) . ' ' . $this->currency . '**')
             ->line('- Data płatności: **' . Carbon::now()->format('d.m.Y') . '**');
        
        if ($endDate) {
            $mail->line('Twoja subskrypcja jest aktywna do **' . $endDate . '**.');
        }
        
        $mail->line('Dziękujemy za korzystanie z naszych usług!')
             ->action('Zarządzaj subskrypcjami', url('/account/subscriptions'));
        
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
            'title' => $this->isRetry ? 'Ponowna płatność powiodła się' : 'Płatność zrealizowana',
            'message' => $this->isRetry 
                ? 'Ponowna próba płatności za subskrypcję ' . $plan->name . ' powiodła się.' 
                : 'Płatność za subskrypcję ' . $plan->name . ' została zrealizowana.',
            'subscription_id' => $this->subscription->id,
            'plan_id' => $plan->id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'is_retry' => $this->isRetry,
            'action_url' => '/account/subscriptions',
        ];
    }
} 