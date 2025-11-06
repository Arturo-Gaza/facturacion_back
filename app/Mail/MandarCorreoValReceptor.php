<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MandarCorreoValReceptor extends Mailable
{
    use Queueable, SerializesModels;
    protected $datosMail;
    /**

     * Create a new message instance.
     */
    public function __construct(array $datosMail)
    {
        $this->datosMail = $datosMail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmar correo',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Asunto del correo')
            ->view('emails.correoValReceptor')
            ->with([
                'datosMail' => $this->datosMail
            ]);
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
