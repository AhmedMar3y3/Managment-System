<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy("created_at", "desc")->get(['id', 'order_type', 'total_price', 'status']);
        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::find($id)->load('sale', 'chef', 'delivery', 'Images');
        return response()->json($order, 200);
    }

    public function newOrders()
    {
        $orders = Order::where('status', 'new')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function completedOrders()
    {
        $orders = Order::where('status', 'completed')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function deliveredOrders()
    {
        $orders = Order::where('status', 'delivered')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function rejectedOrders()
    {
        $orders = Order::where('status', 'delivery declined')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function returnedOrders()
    {
        $orders = Order::where('status', 'returned')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function pendingOrders()
    {
        $orders = Order::where('status', 'inprogress')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
}
