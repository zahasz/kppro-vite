<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserSubscription;

class SubscriptionExpiring extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Użytkownik, do którego wysyłamy e-mail.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Subskrypcja, która wygasa.
     *
     * @var \App\Models\UserSubscription
     */
    public $subscription;

    /**
     * Liczba dni do wygaśnięcia.
     *
     * @var int
     */
    public $daysLeft;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, UserSubscription $subscription, int $daysLeft)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->daysLeft = $daysLeft;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "Ważne: Twoja subskrypcja wygasa za {$this->daysLeft} " . 
            ($this->daysLeft == 1 ? 'dzień' : ($this->daysLeft < 5 ? 'dni' : 'dni'));
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-expiring',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
