<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy("created_at", "desc")->get(['id', 'order_type', 'price', 'status']);
        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::find($id)->load('sale', 'chef', 'delivery', 'Images');
        return response()->json($order, 200);
    }

    public function newOrders()
    {
        $orders = Order::where('status', 'جاري الاستلام')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function completedOrders()
    {
        $orders = Order::where('status', 'تم التجهيز')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function deliveredOrders()
    {
        $orders = Order::where('status', 'تم التوصيل')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function rejectedOrders()
    {
        $orders = Order::where('status', 'رفض السائق')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function returnedOrders()
    {
        $orders = Order::where('status', 'مرتجع')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
    public function pendingOrders()
    {
        $orders = Order::where('status', 'قيد التنفيذ')->orderBy('created_at', 'desc')->get(['id', 'order_type', 'total_price']);
        return response()->json($orders, 200);
    }
}
