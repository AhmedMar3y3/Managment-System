<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Branch;
use App\Models\Chef;
use App\Models\Delivery;
use App\Models\Manager;
use App\Models\Sale;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function stats()
    {
        $sales = Sale::where('status','approved')->count();
        $chefs = Chef::where('status','approved')->count();
        $orders = Order::count();
        $branches = Branch::count();
        $managers = Manager::where('status','approved')->count();
        $deliveries = Delivery::where('status','approved')->count();
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
        $newOrders = Order::where('status', 'new')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $pendingOrders = Order::where('status', 'inprogress')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $returnedOrdersa = Order::where('status', 'returned')->count();
        $declinedOrders = Order::where('status', 'delivery declined')->count();
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
        $sales = Sale::where('status', 'pending')->count();
        $managers = Manager::where('status', 'pending')->count();
        return response()->json([
            'sales' => $sales,
            'managers' => $managers,
        ], 200);
    }

    public function percentages(Request $request)
    {
        $year = $request->input('year');
        $totalOrders = Order::whereYear('created_at', $year)->count();
        $newOrders = Order::whereYear('created_at', $year)->where('status', 'new')->count();
        $completedOrders = Order::whereYear('created_at', $year)->where('status', 'completed')->count();
        $pendingOrders = Order::whereYear('created_at', $year)->where('status', 'inprogress')->count();
        $deliveredOrders = Order::whereYear('created_at', $year)->where('status', 'delivered')->count();
        $returnedOrders = Order::whereYear('created_at', $year)->where('status', 'returned')->count();
        $declinedOrders = Order::whereYear('created_at', $year)->where('status', 'delivery declined')->count();

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
