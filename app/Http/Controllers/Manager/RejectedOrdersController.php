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
        $orders = Order::whereIn('status', ['تم الرفض', 'رفض السائق'])
            ->where('manager_id', auth('manager')->id()) 
            ->get(['id', 'order_type', 'updated_at']);
            $now = now();
    
            $ordersWithDetails = $orders->map(function ($order) use ($now) {
            $timeDifference = $now->diffInMinutes(Carbon::parse($order->updated_at));

            $order->time_difference = $timeDifference . ' ' . 
                ($timeDifference == 1 ? 'دقيقة' : 'دقائق');
            return $order;
        });
        return response()->json([
            'orders' => $ordersWithDetails,
        ], 200);
    } 

//___________________________________show_________________________________________________________________
public function problem(string $id){

        $order = Order::findOrFail($id)->load(['images','chef:id,name', 'delivery:id,name'])
        ->whereIn('status',["رفض السائق","مرتجع"]);
    
        if ($order->manager_id === auth('manager')->id()) {
            return response()->json([
                'success' => true,
                'chef_name' => $order->chef ? $order->chef->first_name : 'لم يتم اختياره بعد',
                'delivery_name' => $order->delivery ? $order->delivery->first_name : 'لم يتم اختياره بعد',
                'additional_data' => $order->additional_data,
                'delivery_date' => $order->delivery_date,
                'customer_phone' => $order->customer_phone,
                'customer_name' => $order->customer_name,
                'order_details' => $order->order_details,
                'order_type' => $order->order_type,
                'created_at' => $order->created_at,
                'remaining' => $order->remaining,
                'deposit' => $order->deposit,
                'images' => $order->images,
                'status' => $order->status,
                'problem' => $order->problem,
                'price' => $order->price,
            ], 200);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'You are not allowed to access this order',
        ], 403);
    }
//________________________________________________________________________________________________________

    // public function deliveryRejectedOrders()
    // {
    //     $orders = Order::where('status', "رفض السائق")
    //         ->where('manager_id', auth('manager')->user()->id)
    //         ->get(['id', 'order_type', 'updated_at']);
    //         $now = now();
    
    //     $ordersWithTimeDifference = $orders->map(function ($order) use ($now) {
    //         $updated_at = $order->updated_at;
    //         $timeDifference = $now->diffInMinutes(Carbon::parse($updated_at));
    
    //         $order->time_difference_in_minutes = $timeDifference . " دقائق ";
    
    //         return $order;
    //     });
    
    //     return response()->json([
    //         'orders' => $ordersWithTimeDifference,
    //     ], 200);
    // }

    //________________________________________________________________________________________________________


}
