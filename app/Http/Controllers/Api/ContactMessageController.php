<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::create([
            'name' => $request->string('name')->toString(),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'message' => $request->string('message')->toString(),
            'status' => 'new',
        ]);

        return response()->json([
            'message' => 'Message sent successfully.',
            'data' => [
                'id' => $message->id,
            ],
        ], 201);
    }
}
