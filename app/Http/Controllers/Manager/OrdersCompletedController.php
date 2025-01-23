<?php
//___________________________________________________________________________________________________________________
namespace App\Http\Controllers\Manager;
//___________________________________________________________________________________________________________________
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderImage;

//___________________________________________________________________________________________________________________
class OrdersCompletedController extends Controller
{
//___________________________________________________________________________________________________________________
    public function completedOrders()
    {
        $manager = auth('manager')->user();
        if (!$manager) {
            return response()->json(['message' => 'لا توجد معلومات '], 403);
        }
    
        $orders = Order::where('manager_id', $manager->id)
            ->where('status', 'تم التجهيز')
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
public function show(string $id)
{
    $order = Order::with(['images', 'manager', 'chef', 'delivery'])->findOrFail($id);

    if ($order->manager_id === auth('manager')->id()) {
        return response()->json([
            'success' => true,
            'delivery_date' => $order->delivery_date,
            'created_at' => $order->created_at,
            'order_details' => $order->order_details,
            'order_type' => $order->order_type,
            'chef_name' => $order->chef ? $order->chef->first_name : 'لم يتم اختياره بعد', // Fixed: Removed extra comma
            'delivery_name' => $order->delivery ? $order->delivery->first_name : 'لم يتم اختياره بعد',
            'customer_phone' => $order->customer_phone,
            'customer_name' => $order->customer_name,
            'price' => $order->price,
            'deposit' => $order->deposit,
            'remaining' => $order->remaining,
            'additional_data' => $order->additional_data,
            'images' => $order->images,
            'status' => $order->status,
        ], 200);
    }

    return response()->json([
        'success' => false,
        'message' => 'You are not allowed to access this order',
    ], 403);
}
//___________________________________________________________________________________________________________________
}


