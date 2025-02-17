<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\Manager\DeliveryAcceptOrder;
use App\Notifications\Manager\OrderCanceled;
use App\Notifications\Manager\orderDeclined;
use App\Notifications\Manager\orderDelivered;
use App\Notifications\Sales\OrderDelivered as SalesOrderDelivered;

class OrderManipulationController extends Controller
{
    public function acceptOrder($id)
    {
        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $order->update(['status' => 'delivery recieved']);
            $manager = $order->manager;
            $manager->notify(new DeliveryAcceptOrder($order));
            return response()->json(['message' => 'Order accepted successfully'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 404);
    }

    public function rejectOrder(Request $request, $id)
    {
        $request->validate([
            'rejection_cause' => 'nullable|string'
        ]);

        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $order->update([
                'status' => 'delivery declined',
                'rejection_cause' => $request->rejection_cause,
                'delivery_id' => null
            ]);
            $manager = $order->manager;
            $manager->notify(new orderDeclined($order));
            return response()->json(['message' => 'Order rejected successfully'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 404);
    }

    public function startRide($id)
    {
        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $order->update(['status' => 'start ride']);
            return response()->json(['message' => 'Ride started successfully'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 404);
    }

    public function orderDelivered($id, Request $request)
    {
        $request->validate([
            'payment_method' => 'nullable|in:cash,visa',
        ]);

        $payment_method = $request->payment_method ?? 'cash';

        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $order->update(['status' => 'delivered', 'payment_method' => $payment_method]);
            $manager = $order->manager;
            $manager->notify(new orderDelivered($order));
            $sales = $order->sales;
            $sales->notify(new SalesOrderDelivered($order));
            return response()->json(['message' => 'Order delivered successfully'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 404);
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $request->validate([
                'problem' => 'nullable|string'
            ]);
            $order->update(['status' => 'returned', 'is_returned' => true, 'problem' => $request->problem]);
            $manager = $order->manager;
            $manager->notify(new OrderCanceled($order));
            return response()->json(['message' => 'Order cancelled successfully'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 404);
    }
}
