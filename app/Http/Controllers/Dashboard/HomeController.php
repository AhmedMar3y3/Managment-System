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
    public function stats()
    {
        $sales = Sale::where('status','مقبول')->count();
        $chefs = Chef::where('status','مقبول')->count();
        $orders = Order::count();
        $branches = Branch::count();
        $managers = Manager::where('status','مقبول')->count();
        $deliveries = Delivery::where('status','مقبول')->count();
        return response()->json([
            'branches' => $branches,
            'managers' => $managers,
            'sales' => $sales,
            'orders' => $orders,
            'chefs' => $chefs,
            'deliveries' => $deliveries,
        ], 200);
    }

    public function orders()
    {
        $newOrders = Order::where('status', 'جاري الاستلام')->count();
        $completedOrders = Order::where('status', 'تم التجهيز')->count();
        $pendingOrders = Order::where('status', 'قيد التنفيذ')->count();
        $deliveredOrders = Order::where('status', 'تم التوصيل')->count();
        $returnedOrdersa = Order::where('status', 'مرتجع')->count();
        $declinedOrders = Order::where('status', 'رفض السائق')->count();
        return response()->json([
            'newOrders' => $newOrders,
            'completedOrders' => $completedOrders,
            'pendingOrders' => $pendingOrders,
            'deliveredOrders' => $deliveredOrders,
            'returnedOrders' => $returnedOrdersa,
            'declinedOrders' => $declinedOrders,
        ], 200);
    }

    public function requests()
    {
        $sales = Sale::where('status', 'قيد الانتظار')->count();
        $managers = Manager::where('status', 'قيد الانتظار')->count();
        return response()->json([
            'sales' => $sales,
            'managers' => $managers,
        ], 200);
    }

    public function percentages(Request $request)
    {
        $year = $request->input('year');
        $totalOrders = Order::whereYear('created_at', $year)->count();
        $newOrders = Order::whereYear('created_at', $year)->where('status', 'جاري الاستلام')->count();
        $completedOrders = Order::whereYear('created_at', $year)->where('status', 'تم التجهيز')->count();
        $pendingOrders = Order::whereYear('created_at', $year)->where('status', 'قيد التنفيذ')->count();
        $deliveredOrders = Order::whereYear('created_at', $year)->where('status', 'تم التوصيل')->count();
        $returnedOrders = Order::whereYear('created_at', $year)->where('status', 'مرتجع')->count();
        $declinedOrders = Order::whereYear('created_at', $year)->where('status', 'رفض السائق')->count();

        $newOrdersPercentage = $totalOrders ? ($newOrders / $totalOrders) * 100 : 0;
        $completedOrdersPercentage = $totalOrders ? ($completedOrders / $totalOrders) * 100 : 0;
        $pendingOrdersPercentage = $totalOrders ? ($pendingOrders / $totalOrders) * 100 : 0;
        $deliveredOrdersPercentage = $totalOrders ? ($deliveredOrders / $totalOrders) * 100 : 0;
        $returnedOrdersPercentage = $totalOrders ? ($returnedOrders / $totalOrders) * 100 : 0;
        $declinedOrdersPercentage = $totalOrders ? ($declinedOrders / $totalOrders) * 100 : 0;

        return response()->json([
            'newOrdersPercentage' => $newOrdersPercentage,
            'completedOrdersPercentage' => $completedOrdersPercentage,
            'pendingOrdersPercentage' => $pendingOrdersPercentage,
            'deliveredOrdersPercentage' => $deliveredOrdersPercentage,
            'returnedOrdersPercentage' => $returnedOrdersPercentage,
            'declinedOrdersPercentage' => $declinedOrdersPercentage,
        ], 200);
    }
}
