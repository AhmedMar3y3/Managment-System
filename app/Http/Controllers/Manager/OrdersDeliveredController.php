<?php
//______________________________________________________________________________________-

namespace App\Http\Controllers\Manager;
//______________________________________________________________________________________-

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Chef;
use App\Models\Manager;
use App\Models\OrderImage;
use App\Models\Delivery;
//______________________________________________________________________________________-

class OrdersDeliveredController extends Controller
{
//______________________________it has been delivered_and ended_______________________________________________________-
public function deliveredOrders()
{
    $manager = auth('manager')->user();
    if (!$manager) {
        return response()->json(['message' => 'لا توجد معلومات '], 403);
    }

    $orders = Order::where('manager_id', $manager->id)
        ->where('status', 'تم التوصيل')
        ->orderBy('delivery_date', 'desc')
        ->get(['id', 'customer_name', 'order_type','status','delivery_date','image']);

    // $ordersWithImages = $orders->map(function ($order) {
    //     $images = OrderImage::where('order_id', $order->id)->first();
    //     $order->images = $images;
    //     return $order;
    // });

    return response()->json([
        'orders' => $orders,
    ]);
}
//______________________________________________________________________________________-
}
