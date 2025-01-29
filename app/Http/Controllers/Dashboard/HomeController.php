<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Chef;
use App\Models\Delivery;
use App\Models\Manager;
use App\Models\Sale;
use App\Models\Order;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function stats(){
        $sales = Sale::count();
        $chefs = Chef::count();
        $orders = Order::count();
        $branches = Branch::count();
        $managers = Manager::count();
        $deliveries = Delivery::count();
        return response()->json([
            'branches'=> $branches,
            'managers' => $managers,
            'sales'=> $sales,
            'orders'=> $orders,
            'chefs'=> $chefs,
            'deliveries'=> $deliveries,
        ],200);
    }

    public function orders(){
        $newOrders = Order::where('status', 'جاري الاستلام')->count();
        $completedOrders = Order::where('status', 'تم التجهيز')->count();
        $pendingOrders = Order::where('status', 'قيد التنفيذ')->count();
        $deliveredOrders = Order::where('status', 'تم التوصيل')->count();
        $returnedOrdersa = Order::where('status', 'مرتجع')->count();
        $declinedOrders = Order::where('status', 'رفض السائق')->count();
        return response()->json([
            'newOrders'=> $newOrders,
            'completedOrders' => $completedOrders,
            'pendingOrders'=> $pendingOrders,
            'deliveredOrders'=> $deliveredOrders,
            'returnedOrders'=> $returnedOrdersa,
            'declinedOrders'=> $declinedOrders,
        ],200);
    }
}
