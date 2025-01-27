<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManagerOrderResource;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
//________________________________________________________________________________________________________
class RejectedOrdersController extends Controller
{
    //________________________________________________________________________________________________________
    public function chefRejectedOrders()
    {
        $orders = Order::where('status', "تم الرفض")
            ->where('manager_id', auth('manager')->user()->id)
            ->get(['id', 'order_type', 'updated_at']);
            $now = now();
    
        $ordersWithTimeDifference = $orders->map(function ($order) use ($now) {
            $updated_at = $order->updated_at;
            $timeDifference = $now->diffInMinutes(Carbon::parse($updated_at));
    
            $order->time_difference_in_minutes = $timeDifference . " دقائق ";
    
            return $order;
        });
    
        return response()->json([
            'orders' => $ordersWithTimeDifference,
        ], 200);
    }
    public function deliveryRejectedOrders()
    {
        $orders = Order::where('status', "رفض السائق")
            ->where('manager_id', auth('manager')->user()->id)
            ->get(['id', 'order_type', 'updated_at']);
            $now = now();
    
        $ordersWithTimeDifference = $orders->map(function ($order) use ($now) {
            $updated_at = $order->updated_at;
            $timeDifference = $now->diffInMinutes(Carbon::parse($updated_at));
    
            $order->time_difference_in_minutes = $timeDifference . " دقائق ";
    
            return $order;
        });
    
        return response()->json([
            'orders' => $ordersWithTimeDifference,
        ], 200);
    }

    //________________________________________________________________________________________________________


}
