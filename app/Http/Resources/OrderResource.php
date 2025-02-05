<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            'order_type' => $this->order_type,
            'order_details' => $this->order_details,
            'price' => $this->total_price,
            'deposit' => $this->deposit,
            'remaining' => $this->remaining,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'longitude' => $this->longitude,
            'latitude'=> $this->latitude,
            'map_desc' => $this->map_desc,
            'additional_data' => $this->additional_data,
            'product' => $this->product,
            'flowers' => $this->flowers,
            'branch' => $this->delivery->branch,
            'status' => $this->status,
        ];
        }
}
