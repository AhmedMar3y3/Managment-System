<?php

namespace App\Http\Controllers\Chef;

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

    public function newOrders()
    {

        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'وافق المدير')->get(['id', 'order_type', 'order_details', 'delivery_date']);
        return response()->json(['orders'=>$orders], 200);
    }
}
