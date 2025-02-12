<?php

namespace App\Http\Controllers\Sales;

use App\Models\Order;
use App\Models\Banner;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function banners()
    {
        $banners = Banner::get(['id', 'image']);
        return response()->json($banners, 200);
    }
    public function readyOrders()
    {
        $orders = Order::where('sale_id', Auth('sale')->id())->count();
        return response()->json(['orders' => $orders], 200);
    }

    public function stats()
    {
        $newOrders = Order::where('sale_id', Auth('sale')->id())->where('status', 'new')->count();
        $inProgressOrders = Order::where('sale_id', Auth('sale')->id())->where('status', 'inprogress')->count();
        $completedOrders = Order::where('sale_id', Auth('sale')->id())->where('status', 'completed')->count();
        return response()->json(['new orders' => $newOrders, 'inprogress orders' => $inProgressOrders, 'completed orders' => $completedOrders], 200);
    }
}
