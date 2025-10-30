<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MandarCorreoFactura extends Mailable
{
    use Queueable, SerializesModels;
    protected $datosMail;
     protected $archivos;
    /**

     * Create a new message instance.
     */
    public function __construct(array $datosMail,$archivos)
    {
        $this->datosMail = $datosMail;
        $this->archivos = $archivos;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
         $mail = $this->subject('Factura')
            ->view('emails.correoFactura')
            ->with([
                'datosMail' => $this->datosMail
            ]);

        // Adjuntar archivos si existen
        if (!empty($this->archivos)) {
            if (is_array($this->archivos)) {
                foreach ($this->archivos as $archivo) {
                    $mail->attach($archivo);
                }
            } else {
                $mail->attach($this->archivos);
            }
        }

        return $mail;
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
