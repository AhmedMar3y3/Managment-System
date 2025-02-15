<?php

namespace App\Http\Controllers\Chef;

use App\Models\Order;
use App\Models\Manager;
use App\Notifications\Manager\orderDone;
use App\Http\Controllers\Controller;
use App\Notifications\Manager\orderAcceptedByChef;

class OrderController extends Controller
{
    public function completedOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'completed')->get(['id', 'updated_at']);
        return response()->json(['orders' => $orders], 200);
    }

    public function acceptedOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'chef approved')->get(['id', 'updated_at']);
        return response()->json(['orders' => $orders], 200);
    }

    public function pendingOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'inprogress')->get(['id', 'updated_at']);
        return response()->json(['orders' => $orders], 200);
    }

    public function getOrderDetails($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())
            ->findOrFail($id)->load('images');
        return response()->json(['order' => $order], 200);
    }

    public function acceptOrder($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'chef waiting') {
            $order->status = 'chef approved';
            $order->save();

            $managerId = $order->manager_id;
            $manager = Manager::where('id', $managerId)->first();
            $manager->notify(new orderAcceptedByChef($order));
            return response()->json(['message' => 'Order status updated'], 200);
        }
        return response()->json(['message' => 'Order status already updated'], 200);
    }

    public function orderInProgress($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'chef approved') {
            $order->status = 'inprogress';
            $order->save();
            return response()->json(['message' => 'Order status updated', 'status' => 1], 200);
        }
        return response()->json(['message' => 'Order status already updated', 'status' => 0], 200);
    }

    public function orderDone($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'inprogress') {
            $order->status = 'completed';
            $order->save();
            $managerId = $order->manager_id;
            $manager = Manager::where('id', $managerId)->first();
            $manager->notify(new orderDone($order));
            return response()->json(['message' => 'Order status updated', 'status' => 1], 200);
        }
        return response()->json(['message' => 'Order status already updated', 'status' => 0], 200);
    }
}
