<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCustomOrderRequest;
use App\Http\Resources\CustomOrderResource;
use App\Models\CustomOrder;

class CustomOrderController extends Controller
{
    public function store(StoreCustomOrderRequest $request)
    {
        $customOrder = CustomOrder::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'preferred_contact_method' => $request->preferred_contact_method,
            'title' => $request->title,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'size' => $request->size,
            'color' => $request->color,
            'budget' => $request->budget,
            'deadline' => $request->deadline,
            'status' => 'new',
        ]);

        return response()->json([
            'message' => 'Custom order created successfully.',
            'data' => new CustomOrderResource($customOrder),
        ], 201);
    }
}
