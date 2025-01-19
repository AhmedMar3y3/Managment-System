<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Banner;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function banners(){
        $banners = Banner::get(['id','image']);
        return response()->json($banners,200);
    }

    public function newOrders(){

        $orders = Order::where('chef_id',Auth('chef')->id())->where('status','وافق المدير')->get(['id','order_type','order_name','delivery_date']);
        return response()->json($orders,200);
    }

    public function stats(){
        $newOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم القبول')->count();
        $inProgressOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'قيد التجهيز')->count();
        $completedOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم التجهيز')->count();
        return response()->json([ 'طلب جديد' => $newOrders, 'طلب قيد التنفيذ' => $inProgressOrders, 'طلب مكتمل' => $completedOrders], 200);
    }



}
