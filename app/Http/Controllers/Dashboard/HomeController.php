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
}
