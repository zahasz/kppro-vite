<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyReportGenerated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Dane raportu tygodniowego
     *
     * @var array
     */
    public $report;

    /**
     * Create a new message instance.
     */
    public function __construct(array $report)
    {
        $this->report = $report;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $startDate = $this->report['period']['start'];
        $endDate = $this->report['period']['end'];
        
        return new Envelope(
            subject: "Raport tygodniowy ({$startDate} - {$endDate})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reports.weekly',
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
