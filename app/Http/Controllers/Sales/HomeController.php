<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Banner;

class HomeController extends Controller
{

    public function banners(){
        $banners = Banner::get(['id','image']);
        return response()->json($banners,200);
    }
    public function readyOrders()
    {
        $orders = Order::where('sale_id', Auth('sale')->id())->where('status', 'تم التجهيز')->with('chef:image','images')->get(['id','order_type','order_name', 'delivery_date']);
        return response()->json(['orders' => $orders], 200);
    }

    public function stats(){

        $newOrders = Order::where('sale_id', Auth('sale')->id())->where('status', 'جاري الاستلام')->count();
        $inProgressOrders = Order::where('sale_id', Auth('sale')->id())->where('status', 'قيد التنفيذ')->count();
        $completedOrders = Order::where('sale_id', Auth('sale')->id())->where('status', 'تم التجهيز')->count();
        return response()->json(['طلب جديد' => $newOrders, 'طلب قيد التنفيذ' => $inProgressOrders, 'طلب مكتمل' => $completedOrders], 200);
    }
}
