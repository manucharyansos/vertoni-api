<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class NewProductNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public string $locale = 'hy',
    ) {}

    public function envelope(): Envelope
    {
        $name = $this->product->getTranslated('name', $this->locale) ?: 'VERTONI';

        return new Envelope(
            subject: match ($this->locale) {
                'ru' => 'Новый товар в VERTONI: ' . $name,
                'en' => 'New in VERTONI: ' . $name,
                default => 'Նոր ապրանք VERTONI-ում՝ ' . $name,
            },
        );
    }

    public function content(): Content
    {
        $slugColumn = 'slug_' . $this->locale;
        $slug = $this->product->{$slugColumn} ?: $this->product->slug_hy ?: $this->product->slug_en ?: $this->product->slug_ru;
        $productUrl = rtrim((string) config('newsletter.frontend_url'), '/') . '/products/' . rawurlencode((string) $slug);

        $shortDescription = $this->product->getTranslated('short_description', $this->locale)
            ?: Str::limit(strip_tags((string) $this->product->getTranslated('description', $this->locale)), 180);

        return new Content(
            view: 'emails.newsletter.new-product',
            with: [
                'locale' => $this->locale,
                'product' => $this->product,
                'productName' => $this->product->getTranslated('name', $this->locale),
                'shortDescription' => $shortDescription,
                'productUrl' => $productUrl,
                'imageUrl' => $this->product->default_image_url,
                'frontendUrl' => rtrim((string) config('newsletter.frontend_url'), '/'),
            ],
        );
    }
}
