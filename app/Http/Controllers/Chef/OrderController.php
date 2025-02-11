<?php

namespace App\Http\Controllers\Chef;

use App\Models\Order;
use App\Models\Manager;
use App\Notifications\orderDone;
use App\Http\Controllers\Controller;
use App\Notifications\orderAcceptedByChef;

class OrderController extends Controller
{
    public function completedOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم التجهيز')->get(['id', 'quantity', 'updated_at']);
        return response()->json(['orders' => $orders], 200);
    }

    public function acceptedOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم القبول')->get(['id', 'quantity', 'updated_at']);
        return response()->json(['orders' => $orders], 200);
    }

    public function pendingOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'قيد التنفيذ')->get(['id', 'quantity', 'updated_at']);
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
        if ($order->status == 'انتظار الشيف') {
            $order->status = 'تم القبول';
            $order->save();

            $managerId = $order->manager_id;
            $manager = Manager::where('id', $managerId)->first();
            $manager->notify(new orderAcceptedByChef($order));
            return response()->json(['message' => 'تم تحديث حالة الطلب'], 200);
        }
        return response()->json(['message' => 'تم تحديث حالة الطلب مسبقاً'], 200);
    }

    public function orderInProgress($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'تم القبول') {
            $order->status = 'قيد التنفيذ';
            $order->save();
            return response()->json(['message' => 'تم تحديث حالة الطلب', 'status' => 1], 200);
        }
        return response()->json(['message' => 'تم تحديث حالة الطلب مسبقاً', 'status' => 0], 200);
    }

    public function orderDone($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'قيد التنفيذ') {
            $order->status = 'تم التجهيز';
            $order->save();
            $managerId = $order->manager_id;
            $manager = Manager::where('id', $managerId)->first();
            $manager->notify(new orderDone($order));
            return response()->json(['message' => 'تم تحديث حالة الطلب', 'status' => 1], 200);
        }
        return response()->json(['message' => 'تم تحديث حالة الطلب مسبقاً', 'status' => 0], 200);
    }
}
