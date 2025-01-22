<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Manager;
use App\Notifications\OrderAccepted;
use App\Notifications\orderAcceptedByChef;
use App\Notifications\orderDeclined;
use App\Notifications\orderDone;

class OrderController extends Controller
{
    public function completedOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم التجهيز')->get(['id','order_name']);
        return response()->json(['orders' => $orders], 200);
    }
    
    // public function newOrders()
    // {
    //     $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'وافق المدير')->get(['id','order_name']);
    //     return response()->json(['orders' => $orders], 200);
    // }
    public function acceptedOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم القبول')->get(['id','order_name']);
        return response()->json(['orders' => $orders], 200);
    }
    
    public function pendingOrders()
    {
        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'قيد التنفيذ')->with('images')->get(['id','order_name']);
        return response()->json(['orders' => $orders], 200);
    }
    //TODO : alter this to get only needed data with images (select is not working with WITH)
    public function getOrderDetails($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())
            ->findOrFail($id)->load('images');
        return response()->json(['order' => $order], 200);
    }

    public function acceptOrder($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'وافق المدير') {
            $order->status = 'تم القبول';
            $order->save();

            $managerId = $order->manager_id;
            $manager = Manager::where('id', $managerId)->first();
            $manager->notify(new orderAcceptedByChef($order));
            return response()->json(['message' => 'تم تحديث حالة الطلب'], 200);
        }
        return response()->json(['message' => 'تم تحديث حالة الطلب مسبقاً'], 200);
    }

    public function declineOrder($id)
    {
        $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($id);
        if ($order->status == 'وافق المدير') {
            $order->status = 'تم الرفض';
            $order->chef_id = null;
            $order->save();
            $managerId = $order->manager_id;
            $manager = Manager::where('id', $managerId)->first();
            $manager->notify(new orderDeclined($order));
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

    // public function submitProblem(){
    //     $data = request()->validate([
    //         'problem' => 'required',
    //         'order_id' => 'required',
    //     ]);
    //     $order = Order::where('chef_id', Auth('chef')->id())->findOrFail($data['order_id']);
    //     $order->problem = $data['problem'];
    //     $order->save();
    //     return response()->json(['message' => 'تم تقديم الشكوى'], 200);
    // }
}
