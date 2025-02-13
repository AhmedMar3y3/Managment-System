<?php

namespace App\Http\Controllers\Manager;

use App\Models\Order;
use App\Models\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrackingPositionController extends Controller
{

    //get all orders with delivery
    public function orderWithDelivery()
    {

        $orders = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', 'delivery recieved')
            ->with(['images' => function ($query) {
                $query->take(1);
            }])
            ->get(['id', 'order_type', 'status', 'delivery_date', 'customer_name']);

        return response()->json([
            'orders' => $orders,
        ], 200);
    }


    //store current location
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'device_id' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        $position = Tracking::create($validatedData);
        return response()->json($position);
    }


    // get latest location
    public function latest($deviceId)
    {
        $position = Tracking::where('device_id', $deviceId)
            ->latest()
            ->first();
        return response()->json($position);
    }
}
