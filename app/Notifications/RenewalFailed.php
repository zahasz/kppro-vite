<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewalFailed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Subskrypcja, której dotyczy powiadomienie
     * 
     * @var UserSubscription
     */
    protected $subscription;

    /**
     * Przyczyna niepowodzenia odnowienia
     * 
     * @var string
     */
    protected $reason;

    /**
     * Utwórz nową instancję powiadomienia.
     *
     * @param UserSubscription $subscription
     * @param string $reason
     * @return void
     */
    public function __construct(UserSubscription $subscription, string $reason = null)
    {
        $this->subscription = $subscription;
        $this->reason = $reason ?? 'Wystąpił problem z płatnością.';
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
        $renewalDate = now()->format('d.m.Y');
        $lastPaymentDate = $this->subscription->payments()
            ->latest()
            ->first()
            ->created_at
            ->format('d.m.Y') ?? 'brak danych';

        return (new MailMessage)
            ->subject('Nieudane odnowienie subskrypcji')
            ->greeting('Witaj ' . $user->name . '!')
            ->line('Niestety, nie udało się automatycznie odnowić Twojej subskrypcji planu ' . $plan->name . '.')
            ->line('Przyczyna: ' . $this->reason)
            ->line('Data próby odnowienia: ' . $renewalDate)
            ->line('Data ostatniej płatności: ' . $lastPaymentDate)
            ->line('Aby kontynuować korzystanie z naszych usług, prosimy o aktualizację informacji o płatności i dokonanie ręcznego odnowienia subskrypcji.')
            ->action('Odnów subskrypcję', url('/subscriptions'))
            ->line('Jeśli masz jakiekolwiek pytania, skontaktuj się z naszym zespołem obsługi klienta.')
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
            'title' => 'Nieudane odnowienie subskrypcji',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan->name,
            'reason' => $this->reason,
            'renewal_date' => now()->toDateTimeString(),
        ];
    }
}
