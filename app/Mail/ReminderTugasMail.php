<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderTugasMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tugas;
    public $jenis;

    public function __construct($tugas, $jenis)
    {
        $this->tugas = $tugas;
        $this->jenis = $jenis;
    }

    public function build()
    {
        $subject = match ($this->jenis) {
            '1_jam' => ' Reminder 1 Jam Lagi',
            '5_menit' => 'Reminder 5 Menit Lagi',
            'deadline' => ' Deadline Tugas Sekarang',
        };

        return $this->subject($subject)
                    ->view('emails.reminder_tugas');
    }
}
