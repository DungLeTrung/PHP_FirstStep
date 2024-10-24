<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $expirationTime;
    /**
     * Create a new message instance.
     */
    public function __construct(string $otpCode, \DateTime $expirationTime)
    {
        $this->otpCode = $otpCode;
        $this->expirationTime = $expirationTime;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your OTP Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $timezone = new \DateTimeZone('Asia/Ho_Chi_Minh');

        $this->expirationTime->setTimezone($timezone);

        return new Content(
            view: 'emails.otp',
            with: [
                'otpCode' => $this->otpCode,
                'expirationTime' => $this->expirationTime->format('H:i'),
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
        return [];
    }
}
