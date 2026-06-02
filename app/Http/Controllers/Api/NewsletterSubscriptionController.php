<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsletterSubscriptionRequest;
use App\Jobs\SendNewsletterWelcomeEmail;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class NewsletterSubscriptionController extends Controller
{
    public function store(StoreNewsletterSubscriptionRequest $request): JsonResponse
    {
        $email = Str::lower($request->string('email')->trim()->toString());

        $subscription = NewsletterSubscription::query()->firstOrNew(['email' => $email]);
        $wasCreated = ! $subscription->exists;
        $shouldSendWelcome = $wasCreated || $subscription->status !== 'active' || ! $subscription->welcome_sent_at;

        $subscription->fill([
            'locale' => $request->input('locale') ?: 'hy',
            'source' => $request->input('source', 'website'),
            'status' => 'active',
            'subscribed_at' => $subscription->subscribed_at ?: now(),
        ])->save();

        if ($shouldSendWelcome) {
            SendNewsletterWelcomeEmail::dispatch($subscription->id)->afterResponse();
        }

        return response()->json([
            'message' => 'Subscription saved successfully.',
            'data' => [
                'id' => $subscription->id,
                'email' => $subscription->email,
                'welcome_email_scheduled' => $shouldSendWelcome,
            ],
        ], $wasCreated ? 201 : 200);
    }
}
