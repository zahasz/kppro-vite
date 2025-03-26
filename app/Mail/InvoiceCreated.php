<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;

class InvoiceCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Faktura, która została wystawiona
     *
     * @var \App\Models\Invoice
     */
    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wystawiono fakturę ' . $this->invoice->number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoices.created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generujemy i dołączamy fakturę w PDF
        $pdf = \PDF::loadView('admin.billing.pdf', ['invoice' => $this->invoice]);
        
        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                "faktura-{$this->invoice->number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
