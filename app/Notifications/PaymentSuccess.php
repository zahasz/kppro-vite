<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Płatność, której dotyczy powiadomienie
     * 
     * @var Payment
     */
    protected $payment;

    /**
     * Czy jest to pierwsza płatność
     * 
     * @var bool
     */
    protected $isFirstPayment;

    /**
     * Utwórz nową instancję powiadomienia.
     *
     * @param Payment $payment
     * @param bool $isFirstPayment
     * @return void
     */
    public function __construct(Payment $payment, bool $isFirstPayment = false)
    {
        $this->payment = $payment;
        $this->isFirstPayment = $isFirstPayment;
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
        $subscription = $this->payment->subscription;
        $plan = $subscription->plan;
        $amount = number_format($this->payment->amount, 2, ',', ' ') . ' ' . $this->payment->currency;
        $paymentDate = $this->payment->created_at->format('d.m.Y H:i');
        $invoiceUrl = route('user.invoices.download', $this->payment->id);
        
        $message = (new MailMessage)
            ->subject($this->isFirstPayment ? 'Dziękujemy za zakup subskrypcji' : 'Potwierdzenie płatności')
            ->greeting('Witaj ' . $user->name . '!');
            
        if ($this->isFirstPayment) {
            $message->line('Dziękujemy za zakup subskrypcji ' . $plan->name . '.')
                ->line('Twoja płatność została pomyślnie zrealizowana, a subskrypcja jest już aktywna.');
        } else {
            $message->line('Potwierdzamy otrzymanie płatności za subskrypcję ' . $plan->name . '.');
        }
        
        $message->line('Szczegóły płatności:')
            ->line('- Kwota: ' . $amount)
            ->line('- Data płatności: ' . $paymentDate)
            ->line('- Metoda płatności: ' . $this->payment->payment_method)
            ->line('- Numer transakcji: ' . $this->payment->transaction_id);
            
        // Link do faktury, jeśli jest dostępna
        if ($this->payment->invoice_number) {
            $message->line('Faktura za zakup (' . $this->payment->invoice_number . ') jest dostępna do pobrania:')
                ->action('Pobierz fakturę', $invoiceUrl);
        }
        
        $message->line('Możesz zarządzać swoją subskrypcją i przeglądać historię płatności w panelu użytkownika.')
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
        $subscription = $this->payment->subscription;
        $plan = $subscription->plan;
        
        return [
            'title' => $this->isFirstPayment ? 'Nowa subskrypcja zakupiona' : 'Płatność zrealizowana',
            'payment_id' => $this->payment->id,
            'subscription_id' => $subscription->id,
            'plan_name' => $plan->name,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'payment_method' => $this->payment->payment_method,
            'transaction_id' => $this->payment->transaction_id,
            'payment_date' => $this->payment->created_at->toDateTimeString(),
            'invoice_number' => $this->payment->invoice_number,
            'is_first_payment' => $this->isFirstPayment,
        ];
    }
} 