<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $locale = 'hy',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: match ($this->locale) {
                'ru' => 'Вы подписались на новости VERTONI',
                'en' => 'You are subscribed to VERTONI updates',
                default => 'Դուք բաժանորդագրվել եք VERTONI-ի նորություններին',
            },
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.welcome',
            with: [
                'locale' => $this->locale,
                'frontendUrl' => rtrim((string) config('newsletter.frontend_url'), '/'),
            ],
        );
    }
}
