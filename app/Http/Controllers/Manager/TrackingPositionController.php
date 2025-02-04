<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tracking;
use App\Models\Order;

class TrackingPositionController extends Controller
{
    
    public function getOrdersWithImages()
    {
    
        $orders = Order::where('manager_id', auth('manager')->user()->id)
        ->where('status', 'استلام السائق')
            ->get(['id', 'order_type', 'status', 'delivery_date', 'customer_name','image']);
        
        return response()->json([
            'orders' => $orders,
        ], 200);
    }  
    
//__________________________________________________________________________________________________

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
    
    public function latest($deviceId)
    {
        $position = Tracking::where('device_id', $deviceId)
            ->latest()
            ->first();
        return response()->json($position);
    }     

}