<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy("created_at", "desc")->get(['id','order_type', 'price', 'status']);
        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::find($id)->load('sale', 'chef', 'delivery', 'Images');
        return response()->json($order, 200);
    }
}
