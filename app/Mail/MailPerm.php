<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailPerm extends Mailable
{
    use Queueable, SerializesModels;

    public $record;

    /**
     * Create a new message instance.
     */
    public function __construct($record)
    {
        $this->record = $record;
        $this->creneaux = $record->creneaux;
        $this->success = $record->validated;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Information sur votre permanence',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $viewName = $this->success ? 'emails.mail_perm_template' : 'emails.mail_perm_fail_template';

        return new Content(
            view: $viewName,
            with: [
                'record' => $this->record,
                'creneaux' => $this->creneaux,  // Passez les créneaux ici
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->success) {
            return [
                Attachment::fromStorage('public/exemple_planning.xlsx')
                    ->as('exemple_planning.xlsx')
                    ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),

                Attachment::fromStorage('public/livre_de_recette_de_teddy.pdf')
                    ->as('livre-de-recette-de-teddy.pdf')
            ];
        }

        return [];
    }
}
