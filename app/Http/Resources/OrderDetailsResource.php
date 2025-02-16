<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'status' => $this->status,
            'order_type' => $this->order_type,
            'order_details' => $this->order_details ?? 'No details',
            'delivery_date' => $this->delivery_date ?? 'Order in same day',
            'from' => $this->from ?? 'Order in same day',
            'to' => $this->to ?? 'Order in same day',
            'description' => $this->description ?? 'No flowers',
            'flower image' => $this->image,
            'cake_price' => $this->cake_price,
            'flower_price' => $this->flower_price,
            'deposit' => $this->deposit,
            'remaining' => $this->total_price - $this->deposit,
            'total_price' => $this->total_price,
            'customer_phone' => $this->customer_phone,
            'customer_name' => $this->customer_name,
            'additional_data' => $this->additional_data,
            'created_at' => $this->created_at,
            'images' => $this->images,
            'problem' => $this->problem ?? 'Order has not been returned',
            'rejection_cause' => $this->rejection_cause ?? 'Order has not been rejected',
            'chef_name' => $this->chef ? $this->chef->first_name : 'Has not been assigned yet',
            'delivery_name' => $this->delivery ? $this->delivery->first_name : 'Has not been assigned yet',
        ];
    }
}
