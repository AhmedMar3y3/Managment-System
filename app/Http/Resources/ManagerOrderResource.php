<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagerOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'created_at'  => $this->created_at,
            'delivery_date'  => $this->delivery_date,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'additional_data' => $this->additional_data,
            'order_details' => $this->order_details,
            'order_type' => $this->order_type,
            'remaining' => $this->remaining,
            'deposit' => $this->deposit,
            'price' => $this->price,
            'chef' => $this->chef ? $this->chef->first_name : 'لم يتم اختياره بعد',

            // 'price' => $this->price,
            // 'longitude' => $this->longitude,
            // 'latitude'=> $this->latitude,
            // 'map_desc' => $this->map_desc,

        ];
    }
}
