<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Chef;
use App\Models\Delivery;
use App\Notifications\orderRecievedToChef;
use App\Notifications\OrderRejectedNotification;

class OrderManipulationController extends Controller
{
    // Accept order
    public function acceptOrder($id)
    {

        $order = Order::findOrFail($id);
        if ($order->status === "جاري الاستلام") {
            $order->status = "وافق المدير";
            $order->manager_id = Auth::guard('manager')->user()->id;
            $order->save();

            return response()->json([
                'message' => 'تم الموافقة على الطلب بنجاح',
                'order_id' => $order->id,
                'manager_id' => $order->manager_id,
                'status' => $order->status,
            ]);
        }
        return response()->json(['message' => 'حالة الطلب غير صحيحة']);
    }


    // Assign order to chef
    public function assignToChef(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'chef_id' => 'required|integer|exists:chefs,id',
        ]);

        $order = Order::find($validatedData['order_id']);
        if ($order) {
            if ($order->status == "وافق المدير") {
                $order->update([
                    'chef_id' => $validatedData['chef_id'],
                ]);
                $chefId = $order->chef_id;
                $chef = Chef::find($chefId);
                $chef->notify(new orderRecievedToChef($order));

                return response()->json(['message' => 'تم ارسال الطلب إلى الشيف بنجاح']);
            }
            return response()->json(['message' => 'في انتظار موافقة الشيف']);
        }
        return response()->json(['message' => 'الطلب غير موجود']);
    }

    // Assign order to delivery

    public function assignOrderToDelivery(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'delivery_id' => 'required|integer|exists:deliveries,id',
        ]);
        $order = Order::find($validatedData['order_id']);
        $order->update([
            'delivery_id' => $validatedData['delivery_id'],
            'status' => 'تم التجهيز',
        ]);

        $delivery = Delivery::find($validatedData['delivery_id']);
        $delivery->notify(new OrderRejectedNotification($order));
        return response()->json(['message' => 'تم إرسال الطلب إلى موظف التوصيل بنجاح'], 200);
    }
}
