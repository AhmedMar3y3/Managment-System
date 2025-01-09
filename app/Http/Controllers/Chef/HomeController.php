<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class HomeController extends Controller
{
    public function homeStats(){
        $newOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم القبول')->count();
        $inProgressOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'جاري التجهيز')->count();
        $completedOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم التجهيز')->count();
        return response()->json([ 'طلب جديد' => $newOrders, 'طلب قيد التنفيذ' => $inProgressOrders, 'طلب مكتمل' => $completedOrders], 200);
    }
}
