<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Manager;
use App\Notifications\OrderAccepted;
use App\Notifications\orderAcceptedByChef;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->get();
        return response()->json(['orders' => $orders], 200);
    }

    public function show($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id)->load('images');
        return response()->json(['order' => $order], 200);
    }

    public function completedOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم التجهيز')->get();
        return response()->json(['orders' => $orders], 200);
    }

    public function newOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'جاري الاستلام')->get();
        return response()->json(['orders' => $orders], 200);
    }

    public function acceptOrder($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'تم القبول') {
            return response()->json(['message' => 'تم تحديث حالة الطلب مسبقاً'], 200);
        }
        $order->status = 'تم القبول';
        $order->save();

        $managerId = $order->manager_id;
        $manager =Manager::where('id', $managerId)->first();
        $manager->notify(new orderAcceptedByChef($order));
        return response()->json(['message' => 'تم تحديث حالة الطلب'], 200);
    }

    public function declineOrder($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'تم الرفض') {
            return response()->json(['message' => 'تم تحديث حالة الطلب مسبقاً'], 200);
        }
        $order->status = 'تم الرفض';
        $order->save();
        return response()->json(['message' => 'تم تحديث حالة الطلب'], 200);
    }

    public function orderInProgress($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'قيد التنفيذ') {
            return response()->json(['message' => 'تم تحديث حالة الطلب مسبقاً'], 200);
        }
        $order->status = 'قيد التنفيذ';
        $order->save();
        return response()->json(['message' => 'تم تحديث حالة الطلب'], 200);
    }

    public function orderDone($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'تم التجهيز') {
            return response()->json(['message' => 'تم تحديث حالة الطلب مسبقاً'], 200);
        }
        $order->status = 'تم التجهيز';
        $order->save();
        return response()->json(['message' => 'تم تحديث حالة الطلب'], 200);
    }
}
