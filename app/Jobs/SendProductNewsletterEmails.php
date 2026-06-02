<?php

namespace App\Jobs;

use App\Mail\NewProductNewsletterMail;
use App\Models\NewsletterSubscription;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendProductNewsletterEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 300;

    public function __construct(public int $productId) {}

    public function handle(): void
    {
        if (! config('newsletter.mail_enabled') || ! config('newsletter.new_product_email_enabled')) {
            return;
        }

        $product = Product::query()
            ->with(['category', 'images', 'activeVariants'])
            ->find($this->productId);

        if (! $product || ! $product->is_active) {
            return;
        }

        $batchSize = max(10, (int) config('newsletter.batch_size', 50));
        $sent = 0;

        NewsletterSubscription::query()
            ->where('status', 'active')
            ->whereNotNull('email')
            ->orderBy('id')
            ->chunkById($batchSize, function ($subscriptions) use ($product, &$sent) {
                foreach ($subscriptions as $subscription) {
                    try {
                        Mail::to($subscription->email)->send(
                            new NewProductNewsletterMail($product, $subscription->locale ?: 'hy')
                        );

                        $sent++;
                    } catch (\Throwable $exception) {
                        Log::warning('New product newsletter email failed.', [
                            'product_id' => $this->productId,
                            'subscription_id' => $subscription->id,
                            'email' => $subscription->email,
                            'error' => $exception->getMessage(),
                        ]);
                    }
                }
            });

        $product->forceFill([
            'newsletter_sent_at' => now(),
        ])->save();

        Log::info('New product newsletter emails processed.', [
            'product_id' => $this->productId,
            'sent_count' => $sent,
        ]);
    }
}
