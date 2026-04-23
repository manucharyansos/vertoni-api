<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsletterSubscriptionRequest;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class NewsletterSubscriptionController extends Controller
{
    public function store(StoreNewsletterSubscriptionRequest $request): JsonResponse
    {
        $email = Str::lower($request->string('email')->trim()->toString());

        $subscription = NewsletterSubscription::updateOrCreate(
            ['email' => $email],
            [
                'locale' => $request->input('locale'),
                'source' => $request->input('source', 'website'),
                'status' => 'active',
                'subscribed_at' => now(),
            ],
        );

        return response()->json([
            'message' => 'Subscription saved successfully.',
            'data' => [
                'id' => $subscription->id,
                'email' => $subscription->email,
            ],
        ], $subscription->wasRecentlyCreated ? 201 : 200);
    }
}
