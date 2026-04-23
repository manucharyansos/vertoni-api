<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'preferred_contact_method' => $this->preferred_contact_method,
            'title' => $this->title,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'size' => $this->size,
            'color' => $this->color,
            'budget' => $this->budget,
            'deadline' => $this->deadline?->format('Y-m-d'),
            'status' => $this->status,
            'admin_note' => $this->admin_note,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
