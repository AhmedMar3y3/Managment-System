<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    // new not assigned orders
    public function managerAcceptedOrders(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to = Carbon::parse($request->to)->endOfDay();

        $orders = Order::where('manager_id', auth('manager')->id())
            ->where('status', 'manager accepted')
            ->whereBetween('delivery_date', [$from, $to])
            ->with(['images' => function ($query) {
                $query->take(1);
            }])
            ->get(['id', 'customer_name', 'order_type', 'status', 'delivery_date']);
        return response()->json([
            'orders' => $orders
        ], 200);
    }
    // completed orders
    public function completedOrders(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to = Carbon::parse($request->to)->endOfDay();

        $manager = auth('manager')->user();
        if (!$manager) {
            return response()->json(['message' => 'No information'], 403);
        }

        $orders = Order::where('manager_id', $manager->id)
            ->where('status', 'completed')
            ->orderBy('delivery_date', 'desc')
            ->whereBetween('delivery_date', [$from, $to])
            ->with(['images' => function ($query) {
                $query->take(1);
            }])
            ->get(['id', 'customer_name', 'order_type', 'status', 'delivery_date']);

        return response()->json([
            'orders' => $orders,

        ], 200);
    }

    // delivered orders

    public function deliveredOrders(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to = Carbon::parse($request->to)->endOfDay();
        $manager = auth('manager')->user();
        if (!$manager) {
            return response()->json(['message' => 'No information'], 403);
        }

        $orders = Order::where('manager_id', $manager->id)
            ->where('status', 'delivered')
            ->orderBy('delivery_date', 'desc')
            ->whereBetween('delivery_date', [$from, $to])
            ->with(['images' => function ($query) {
                $query->take(1);
            }])
            ->get(['id', 'customer_name', 'order_type', 'status', 'delivery_date']);

        return response()->json([
            'orders' => $orders,
        ]);
    }

    // show order
    public function show(string $id)
    {
        $order = Order::findOrFail($id)->load(['images', 'chef:id,first_name', 'delivery:id,first_name']);

        if ($order->manager_id === auth('manager')->id() || $order->status === 'new') {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'order_type' => $order->order_type,
                'order_details' => $order->order_details ?? 'No details',
                'delivery_date' => $order->delivery_date ?? 'Order in same day',
                'delivery_time' => $order->delivery_time ?? 'Order in same day',
                'description' => $order->description ?? 'No flowers',
                'flower image' => $order->image ?? 'No flowers',
                'cake_price' => $order->cake_price,
                'flower_price' => $order->flower_price,
                'deposit' => $order->deposit,
                'remaining' => $order->total_price - $order->deposit,
                'total_price' => $order->total_price,
                'customer_phone' => $order->customer_phone,
                'customer_name' => $order->customer_name,
                'additional_data' => $order->additional_data,
                'created_at' => $order->created_at,
                'images' => $order->images,
                'problem' => $order->problem ?? 'Order has not been returned',
                'rejection_cause' => $order->rejection_cause ?? 'Order has not been rejected',
                'chef_name' => $order->chef ? $order->chef->first_name : 'Has not been assigned yet',
                'delivery_name' => $order->delivery ? $order->delivery->first_name : 'Has not been assigned yet',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'You are not allowed to access this order',
        ], 403);
    }

    // rejected orders
    public function deliveryRejectedOrders(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to = Carbon::parse($request->to)->endOfDay();

        $orders = Order::whereIn('status', ['delivery declined'])
            ->where('manager_id', auth('manager')->id())
            ->whereBetween('delivery_date', [$from, $to])
            ->with(['images' => function ($query) {
                $query->take(1);
            }])
            ->get(['id', 'order_type', 'updated_at', 'status']);
        $now = now();
        $ordersWithDetails = $orders->map(function ($order) use ($now) {
            $updatedAt = Carbon::parse($order->updated_at);
            $diffHours = $updatedAt->diffInHours($now);
            $diffMinutes = $updatedAt->diffInMinutes($now) % 60;

            $order->time_difference =  $diffHours . 'minute ' . $diffMinutes . ' and ' . 'hour';
            return $order;
        });

        return response()->json([
            'orders' => $ordersWithDetails,
        ], 200);
    }

    // returned orders
    public function returnedOrders(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to = Carbon::parse($request->to)->endOfDay();

        $order = Order::where('status', 'returned')
            ->where('manager_id', auth('manager')->user()->id)
            ->with(['images' => function ($query) {
                $query->take(1);
            }])
            ->get(['id', 'customer_name', 'order_type', 'status', 'delivery_date', 'image']);

        if (!$order) {
            return response()->json([
                'message' => 'No information available',
            ], 404);
        }

        return response()->json([
            'orders' => $order,
        ], 200);
    }
}
