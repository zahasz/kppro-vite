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
use App\Models\SubscriptionPayment;

class SubscriptionRenewed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Użytkownik, do którego wysyłamy e-mail.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Subskrypcja, która została odnowiona.
     *
     * @var \App\Models\UserSubscription
     */
    public $subscription;

    /**
     * Płatność za odnowienie subskrypcji.
     *
     * @var \App\Models\SubscriptionPayment|null
     */
    public $payment;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, UserSubscription $subscription, ?SubscriptionPayment $payment = null)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->payment = $payment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Potwierdzenie odnowienia subskrypcji',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-renewed',
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
