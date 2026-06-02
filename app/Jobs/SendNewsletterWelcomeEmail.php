<?php

namespace App\Jobs;

use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletterWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 60;

    public function __construct(public int $subscriptionId) {}

    public function handle(): void
    {
        if (! config('newsletter.mail_enabled') || ! config('newsletter.welcome_email_enabled')) {
            return;
        }

        $subscription = NewsletterSubscription::query()->find($this->subscriptionId);

        if (! $subscription || $subscription->status !== 'active') {
            return;
        }

        try {
            Mail::to($subscription->email)->send(new NewsletterWelcomeMail($subscription->locale ?: 'hy'));

            $subscription->forceFill([
                'welcome_sent_at' => now(),
            ])->save();
        } catch (\Throwable $exception) {
            Log::warning('Newsletter welcome email failed.', [
                'subscription_id' => $this->subscriptionId,
                'email' => $subscription->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
