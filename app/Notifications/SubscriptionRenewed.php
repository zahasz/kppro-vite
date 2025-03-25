<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Subskrypcja, której dotyczy powiadomienie
     * 
     * @var UserSubscription
     */
    protected $subscription;

    /**
     * Płatność związana z odnowieniem
     * 
     * @var Payment|null
     */
    protected $payment;

    /**
     * Utwórz nową instancję powiadomienia.
     *
     * @param UserSubscription $subscription
     * @param Payment|null $payment
     * @return void
     */
    public function __construct(UserSubscription $subscription, Payment $payment = null)
    {
        $this->subscription = $subscription;
        $this->payment = $payment;
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
        $endDate = $this->subscription->ends_at ? $this->subscription->ends_at->format('d.m.Y') : 'bezterminowo';
        $startDate = $this->subscription->starts_at->format('d.m.Y');
        
        $message = (new MailMessage)
            ->subject('Subskrypcja została odnowiona')
            ->greeting('Witaj ' . $user->name . '!')
            ->line('Twoja subskrypcja planu ' . $plan->name . ' została pomyślnie odnowiona.');
        
        if ($this->payment) {
            $amount = number_format($this->payment->amount, 2, ',', ' ') . ' ' . $this->payment->currency;
            $method = $this->payment->payment_method;
            
            $message->line('Szczegóły płatności:')
                ->line('- Kwota: ' . $amount)
                ->line('- Metoda płatności: ' . $method)
                ->line('- Numer transakcji: ' . $this->payment->transaction_id)
                ->line('- Data płatności: ' . $this->payment->created_at->format('d.m.Y H:i'));
        }
        
        $message->line('Okres subskrypcji: ' . $startDate . ' - ' . $endDate)
            ->line('Możesz zarządzać swoją subskrypcją w panelu użytkownika.')
            ->action('Zarządzaj subskrypcjami', url('/subscriptions'))
            ->line('Dziękujemy za korzystanie z naszych usług!')
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
        
        $data = [
            'title' => 'Subskrypcja została odnowiona',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan->name,
            'starts_at' => $this->subscription->starts_at->toDateTimeString(),
            'ends_at' => $this->subscription->ends_at?->toDateTimeString(),
        ];
        
        if ($this->payment) {
            $data['payment_id'] = $this->payment->id;
            $data['amount'] = $this->payment->amount;
            $data['currency'] = $this->payment->currency;
            $data['payment_method'] = $this->payment->payment_method;
            $data['transaction_id'] = $this->payment->transaction_id;
        }
        
        return $data;
    }
} 