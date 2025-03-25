<?php

namespace App\Notifications;

use App\Models\UserSubscription;
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
     * Przyczyna niepowodzenia
     * 
     * @var string
     */
    protected $reason;

    /**
     * Liczba prób płatności
     * 
     * @var int
     */
    protected $attemptCount;

    /**
     * Utwórz nową instancję powiadomienia.
     *
     * @param UserSubscription $subscription
     * @param float $amount
     * @param string $currency
     * @param string $reason
     * @param int $attemptCount
     * @return void
     */
    public function __construct(UserSubscription $subscription, float $amount, string $currency = 'PLN', string $reason = null, int $attemptCount = 1)
    {
        $this->subscription = $subscription;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->reason = $reason ?? 'Wystąpił problem z płatnością.';
        $this->attemptCount = $attemptCount;
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
        $amount = number_format($this->amount, 2, ',', ' ') . ' ' . $this->currency;
        $paymentDate = now()->format('d.m.Y H:i');
        
        $message = (new MailMessage)
            ->subject('Problem z płatnością za subskrypcję')
            ->greeting('Witaj ' . $user->name . '!');
            
        if ($this->attemptCount == 1) {
            $message->line('Podczas próby pobrania płatności za subskrypcję planu ' . $plan->name . ' wystąpił problem.');
        } else {
            $message->line('Kolejna próba pobrania płatności za subskrypcję planu ' . $plan->name . ' nie powiodła się.');
        }
        
        $message->line('Szczegóły:')
            ->line('- Kwota: ' . $amount)
            ->line('- Data próby: ' . $paymentDate)
            ->line('- Przyczyna: ' . $this->reason);
        
        if ($this->subscription->grace_period_ends_at) {
            $graceEndDate = $this->subscription->grace_period_ends_at->format('d.m.Y');
            $message->line('Twoja subskrypcja pozostanie aktywna w okresie karencji do ' . $graceEndDate . '.');
        }
        
        $message->line('Aby uniknąć zawieszenia subskrypcji, prosimy o aktualizację metody płatności lub dokonanie ręcznej płatności.')
            ->action('Aktualizuj metodę płatności', url('/subscription/payment-methods'))
            ->line('Jeśli potrzebujesz pomocy, skontaktuj się z naszym zespołem obsługi klienta.')
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
            'title' => 'Nieudana płatność za subskrypcję',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan->name,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'reason' => $this->reason,
            'attempt_count' => $this->attemptCount,
            'payment_date' => now()->toDateTimeString(),
            'grace_period_ends_at' => $this->subscription->grace_period_ends_at?->toDateTimeString(),
        ];
    }
} 