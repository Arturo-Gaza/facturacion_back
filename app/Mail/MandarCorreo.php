<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MandarCorreo extends Mailable
{
    use Queueable, SerializesModels;
    protected $datosUsr;
    protected $datosSol;
    /**

     * Create a new message instance.
     */
    public function __construct(array $datosUsr, array $datosSol)
    {
        $this->datosUsr = $datosUsr;
        $this->datosSol = $datosSol;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualización de solicitud',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Asunto del correo')
            ->view('emails.correo')
            ->with([
                'datosUsr' => $this->datosUsr,
                'datosSol' => $this->datosSol,
                'link'=>config('app.url')
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
