<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tracking;
use App\Models\Order;
use App\Models\OrderImage;

class TrackingPositionController extends Controller
{
    
//__________________________________________________________________________________________________
    public function getOrdersWithImages()
    {
    
        $orders = Order::where('status', 'استلام السائق')
            ->get(['id', 'order_type', 'status', 'delivery_date', 'customer_name']);
    
        $ordersWithImages = $orders->map(function ($order) {

            $orderImage = OrderImage::where('order_id', $order->id)->value('image'); 
            return [
                'order' => $order,
                'image' => $orderImage, 
            ];
        });
        
        return response()->json([
            'orders' => $ordersWithImages,
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
    



//__________________________________________________________________________________________________

    // public function store(Request $request)
    // {
    //     // التحقق من صحة البيانات المدخلة
    //     $validatedData = $request->validate([
    //         'device_id' => 'required', 
    //         'latitude' => 'required|numeric',
    //         'longitude' => 'required|numeric',
    //         'order_id' => 'required|exists:orders,id', // التأكد من وجود order_id
    //     ]);

    //     // حفظ البيانات في جدول التتبع
    //     $position = Tracking::create($validatedData);

    //     // جلب الطلب المرتبط بـ order_id
    //     $order = Order::find($validatedData['order_id']);

    //     // إنشاء رابط الخريطة
    //     $mapUrl = "https://www.google.com/maps?q={$validatedData['latitude']},{$validatedData['longitude']}&z=15";

    //     // إرجاع البيانات مع رابط الخريطة
    //     return response()->json([
    //         'position' => $position,
    //         'order' => $order,
    //         'map' => $mapUrl,
    //     ], 201);
    // }

    // public function latest($deviceId)
    // {
    //     $position = Tracking::where('device_id', $deviceId)
    //         ->latest()
    //         ->first();
    //     return response()->json($position);
    // }     
//_______________________________________________________________________________________

}