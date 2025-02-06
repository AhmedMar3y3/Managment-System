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
            'payment_method' => 'nullable|in:cash,visa',
        ]);

        $payment_method = $request->payment_method ?? 'cash';
        

        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $order->update(['status' => 'تم التوصيل', 'payment_method' => $request->payment_method]);
            return response()->json(['message' => 'تم توصيل الطلب بنجاح'], 200);
        }
        return response()->json(['message' => 'غير مصرح'], 404);
    }

    // public function orderDelivered($id, Request $request)
    // {
    //     // Validate the request to include the payment method and the current delivery coordinates.
    //     $request->validate([
    //         'payment_method' => 'required|in:cash,visa',
    //         'lat'            => 'required|numeric',
    //         'lng'            => 'required|numeric',
    //     ]);

    //     // Find the order by ID.
    //     $order = Order::find($id);
    //     if (!$order) {
    //         return response()->json(['message' => 'Order not found'], 404);
    //     }

    //     // Check that the current delivery person is assigned to this order.
    //     if ($order->delivery_id != auth('delivery')->id()) {
    //         return response()->json(['message' => 'غير مصرح'], 404);
    //     }

    //     // Calculate the distance between the delivery person's current location (from the request)
    //     // and the order's stored coordinates.
    //     // Make sure your order model has 'latitude' and 'longitude' attributes.
    //     $distance = DistanceHelper::haversineDistance(
    //         $request->input('lat'),
    //         $request->input('lng'),
    //         $order->latitude,  // order stored latitude
    //         $order->longitude  // order stored longitude
    //     );

    //     // Check if the distance is 20 meters or less (0.02 kilometers).
    //     if ($distance <= 0.02) {
    //         $order->update([
    //             'status'         => 'تم التوصيل',
    //             'payment_method' => $request->input('payment_method'),
    //         ]);

    //         return response()->json(['message' => 'تم توصيل الطلب بنجاح'], 200);
    //     } else {
    //         return response()->json(['message' => 'لم تصل إلى المكان المطلوب'], 400);
    //     }
    // }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order->delivery_id == Auth('delivery')->id()) {
            $request->validate([
                'problem' => 'nullable|string'
            ]);
            $order->update(['status' => 'مرتجع', 'is_returned' => true, 'problem' => $request->problem]);
            return response()->json(['message' => 'تم بنجاح'], 200);
        }
        return response()->json(['message' => 'غير مصرح'], 404);
    }
}
