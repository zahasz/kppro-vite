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

class SubscriptionExpired extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Użytkownik, do którego wysyłamy e-mail.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Subskrypcja, która wygasła.
     *
     * @var \App\Models\UserSubscription
     */
    public $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, UserSubscription $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Twoja subskrypcja wygasła',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-expired',
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
