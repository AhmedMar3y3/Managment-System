<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class HomeController extends Controller
{
    // Home Page stats
    public function stats()
    {

        $preparedCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', "تم التجهيز")
            ->count();

        $rejectedCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', 'رفض السائق')
            ->count();

        $deliveredCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', "تم التوصيل")
            ->count();

        $returnedCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', "مرتجع")
            ->count();


        $reciveCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', 'استلام السائق')
            ->count();


        $Count = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', "تم التوصيل")
            ->count();

        $totalOrders = Order::count();
        $Percentage = ($totalOrders > 0) ? ($Count / $totalOrders) * 100 : 0;



        return response()->json([
            'prepared' => $preparedCount,
            'rejected' => $rejectedCount,
            'delivered' => $deliveredCount,
            'returned' => $returnedCount,
            'recive' =>  $reciveCount,
            'percentage' => $Percentage . "%",
        ], 200);
    }

    // in progress orders
    public function inProgressOrders()
    {
        $orders = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status',  'قيد التنفيذ')
            ->orderBy('delivery_date', 'desc')
            ->get(['id', 'customer_name', 'order_details', 'order_type']);

        return response()->json([

            'orders' => $orders,
            'rate' => 50
        ], 200);
    }

    // new orders
    public function NewOrders()
    {
        $orders = Order::where('status', 'جاري الاستلام')
            ->whereNotNull('customer_name') 
            ->orderBy('created_at', 'desc')
            ->get(['id', 'order_type', 'delivery_date', 'order_details']);
        return response()->json([
            'orders' => $orders
        ], 200);
    }

    // show new order
    public function ShowNewOrder(string $id)
    {
        $orders = Order::findOrFail($id)->load('Images', 'flowers');
        return response()->json([
            'orders' => $orders,
        ], 200);
    }
}
