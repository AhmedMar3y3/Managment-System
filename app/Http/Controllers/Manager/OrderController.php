<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use App\Models\Order;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    // new not assigned orders
    public function managerAcceptedOrders()
    {
        $orders = Order::where('manager_id', auth('manager')->id())
            ->where('status', 'manager accepted')
            ->orderBy('delivery_date', 'desc')
            ->with('images')
            ->get(['id', 'customer_name', 'order_type', 'status', 'delivery_date']);
        return response()->json([$orders], 200);
    }
    // completed orders
    public function completedOrders()
    {
        $manager = auth('manager')->user();
        if (!$manager) {
            return response()->json(['message' => 'No information'], 403);
        }

        $orders = Order::where('manager_id', $manager->id)
            ->where('status', 'completed')
            ->orderBy('delivery_date', 'desc')
            ->get(['id', 'customer_name', 'order_type', 'status', 'delivery_date', 'image']);

        return response()->json([
            'orders' => $orders,

        ], 200);
    }

    // delivered orders

    public function deliveredOrders()
    {
        $manager = auth('manager')->user();
        if (!$manager) {
            return response()->json(['message' => 'No information'], 403);
        }

        $orders = Order::where('manager_id', $manager->id)
            ->where('status', 'delivered')
            ->orderBy('delivery_date', 'desc')
            ->get(['id', 'customer_name', 'order_type', 'status', 'delivery_date', 'image']);

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
                'delivery_date' => $order->delivery_date,
                'created_at' => $order->created_at,
                'order_details' => $order->order_details,
                'order_type' => $order->order_type,
                'chef_name' => $order->chef ? $order->chef->first_name : 'Has not been assigned yet',
                'delivery_name' => $order->delivery ? $order->delivery->first_name : 'Has not been assigned yet',
                'customer_phone' => $order->customer_phone,
                'customer_name' => $order->customer_name,
                'cake_price' => $order->cake_price,
                'deposit' => $order->deposit,
                'additional_data' => $order->additional_data,
                'images' => $order->images,
                'status' => $order->status,
                'problem' => $order->problem,
                'rejection_cause' => $order->rejection_cause,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'You are not allowed to access this order',
        ], 403);
    }

    // rejected orders
    public function deliveryRejectedOrders()
    {
        $orders = Order::whereIn('status', ['delivery declined'])
            ->where('manager_id', auth('manager')->id())
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
    public function returnRequests()
    {
        $order = Order::where('status', 'returned')
            ->where('manager_id', auth('manager')->user()->id)
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
