<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;

class HomeController extends Controller
{
    // Home Page stats
    public function stats()
    {

        $preparedCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', "completed")
            ->count();

        $rejectedCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', 'delivery declined')
            ->count();

        $deliveredCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', "delivered")
            ->count();

        $returnedCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', "returned")
            ->count();


        $reciveCount = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', 'delivery recieved')
            ->count();


        $Count = Order::where('manager_id', auth('manager')->user()->id)
            ->where('status', "delivered")
            ->count();

        $notAssignedOrders = Order::where("manager_id", auth("manager")->user()->id)
            ->where("status", "manager accepted")->count();

        $assignedOrders = Order::where("manager_id", auth("manager")->user()->id)
            ->whereIn("status", ['chef waiting' , 'delivery waiting'])->count();

        $totalOrders = Order::count();
        $Percentage = ($totalOrders > 0) ? ($Count / $totalOrders) * 100 : 0;

        return response()->json([
            'prepared' => $preparedCount,
            'rejected' => $rejectedCount,
            'delivered' => $deliveredCount,
            'returned' => $returnedCount,
            'recive' =>  $reciveCount,
            'not_assigned' => $notAssignedOrders,
            'assigned_orders'=> $assignedOrders,
            'percentage' => $Percentage . "%",
        ], 200);
    }

    // in progress orders
    public function inProgressOrders(Request $request)
    {


        $orders = Order::where('manager_id', Auth::guard('manager')->id())
            ->where('status', 'inprogress')
            ->get(['id', 'customer_name', 'order_details', 'order_type']);

        if ($request->has('from') && $request->has('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $orders->whereBetween('delivery_date', [$from, $to]);
        }

        return response()->json([
            'orders' => $orders,
        ], 200);
    }
    // new orders
    public function NewOrders(Request $request)
    {
        $orders = Order::where('status', 'new')
            ->whereNotNull('customer_name')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'order_type', 'delivery_date', 'order_details']);

        if ($request->has('from') && $request->has('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $orders->whereBetween('delivery_date', [$from, $to]);
        }
        return response()->json([
            'orders' => $orders
        ], 200);
    }

    // show new order
    public function ShowNewOrder(string $id)
    {
        $orders = Order::findOrFail($id)->load('Images');
        return response()->json([
            'orders' => $orders,
        ], 200);
    }
}
