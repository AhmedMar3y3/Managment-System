<?php

namespace App\Http\Controllers\Delivery;

use App\Models\DeliveryPosition;
use App\Http\Controllers\Controller;
use App\Http\Requests\tracking\storePositionRequest;

class TrackingController extends Controller
{
    public function store(storePositionRequest $request)
    {
        $delivery = Auth('delivery')->user();
        DeliveryPosition::create(array_merge($request->validated(), ['delivery_id' => $delivery->id]));
        return response()->json(['message' => 'Location stored successfully'], 200);
    }

    public function latest()
    {
        $delivery = Auth('delivery')->user();
        $position = DeliveryPosition::where('delivery_id', $delivery->id)->latest()->first();
        return response()->json($position);
    }
}
