<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SubscriptionRenewalReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Subskrypcja, której dotyczy powiadomienie
     *
     * @var UserSubscription
     */
    protected $subscription;

    /**
     * Tworzenie nowej instancji powiadomienia.
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
        $endDate = Carbon::parse($this->subscription->end_date);
        $daysLeft = Carbon::now()->diffInDays($endDate);
        
        return (new MailMessage)
            ->subject('Przypomnienie o zbliżającym się odnowieniu subskrypcji')
            ->greeting('Witaj ' . $notifiable->name . '!')
            ->line('Przypominamy, że Twoja subskrypcja **' . $plan->name . '** zostanie automatycznie odnowiona za ' . $daysLeft . ' dni.')
            ->line('Data automatycznego odnowienia: **' . $endDate->format('d.m.Y') . '**')
            ->line('Twoja karta zostanie obciążona kwotą ' . number_format($plan->price, 2) . ' ' . ($plan->currency ?? 'PLN') . '.')
            ->line('Jeśli nie chcesz kontynuować subskrypcji, możesz ją anulować w dowolnym momencie przed datą odnowienia.')
            ->action('Zarządzaj subskrypcją', url('/account/subscriptions'))
            ->line('Dziękujemy za korzystanie z naszych usług!');
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
        $endDate = Carbon::parse($this->subscription->end_date);
        $daysLeft = Carbon::now()->diffInDays($endDate);
        
        return [
            'title' => 'Zbliża się odnowienie subskrypcji',
            'message' => 'Twoja subskrypcja ' . $plan->name . ' zostanie odnowiona za ' . $daysLeft . ' dni.',
            'subscription_id' => $this->subscription->id,
            'renewal_date' => $endDate->format('Y-m-d'),
            'amount' => $plan->price,
            'currency' => $plan->currency ?? 'PLN',
            'action_url' => '/account/subscriptions',
        ];
    }
} 