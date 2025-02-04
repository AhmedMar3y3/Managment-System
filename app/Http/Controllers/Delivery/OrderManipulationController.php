<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderManipulationController extends Controller
{
    public function acceptOrder($id)
    {
        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $order->update(['status' => 'استلام السائق']);
            return response()->json(['message' => 'تم قبول الطلب بنجاح'], 200);
        }
        return response()->json(['message' => 'غير مصرح'], 404);
    }
    public function rejectOrder(Request $request, $id)
    {
        $request->validate([
            'rejection_cause' => 'nullable|string'
        ]);

        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $order->update([
                'status' => 'رفض السائق',
                'rejection_cause' => $request->rejection_cause,
                'delivery_id' => null
            ]);
            return response()->json(['message' => 'تم رفض الطلب بنجاح'], 200);
        }
        return response()->json(['message' => 'غير مصرح'], 404);
    }

    public function orderDelivered($id, Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,visa',
        ]);

        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $order->update(['status' => 'تم التوصيل', 'payment_method' => $request->payment_method]);
            return response()->json(['message' => 'تم توصيل الطلب بنجاح'], 200);
        }
        return response()->json(['message' => 'غير مصرح'], 404);
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $request->validate([
                'problem' => 'required|string'
            ]);
            $order->update(['status' => 'مرتجع', 'is_returned' => true, 'problem' => $request->problem]);
            return response()->json(['message' => 'تم بنجاح'], 200);
        }
        return response()->json(['message' => 'غير مصرح'], 404);
    }
}
