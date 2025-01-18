<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Manager;
use App\Models\Order;
use App\Models\Chef;
use App\Models\OrderImage;


class OrdersNotFinishedController extends Controller
{
    public function OrdersNotFinished()
    {
        $manager = auth('manager')->user();
        if (!$manager) {
            return response()->json(['message' => 'لا توجد معلومات '], 403);
        }
    
        $orders = Order::where('manager_id', $manager->id)
            ->where('status',  'تم القبول')
            ->orderBy('delivery_date', 'desc')
            ->get(['id', 'customer_name', 'order_details', 'order_type']);

        $ordersWithImages = $orders->map(function ($order) {
            $images = OrderImage::where('order_id', $order->id)->first();
            $order->images = $images;
            return $order;
        });
    
        return response()->json([
            'orders' => $ordersWithImages,
        ]);
    }
//___________________________________________________________________________________________________________________
public function OrdersNotFinishedDetails (string $id)
{
    $order = Order::with(['images', 'manager'])->findOrFail($id);
    if ($order->manager_id === auth('manager')->id()) {
        return response()->json([
            'success' => true,
            'data' => $order,
        ], 200);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'You are not allowed to access this order',
    ], 403);
}

//___________________________________________________________________________________________________________________

}
