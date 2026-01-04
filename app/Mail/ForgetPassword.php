<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $otp;
    public function __construct($otp)
    {
        $this->otp = $otp;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Forget Passsword',
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'email',
        );
    }
    public function attachments(): array
    {
        return [];
    }

}
