<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl; // Harus public agar terbaca otomatis oleh view

    public function __construct($resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Akun RememberME',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_password', // Pastikan jalur view benar
        );
    }
}