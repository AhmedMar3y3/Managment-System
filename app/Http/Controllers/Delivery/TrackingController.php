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
        DeliveryPosition::create([$request->validated() + 'delivery_id' => $delivery->id]);
        return response()->json(['message' =>'تم تخزين الموقع بنجاح'],200);
    }

    public function latest()
    {
        $delivery = Auth('delivery')->user();
        $position = DeliveryPosition::where('delivery_id', $delivery->id)->latest()->first();
        return response()->json($position);
    }
}
