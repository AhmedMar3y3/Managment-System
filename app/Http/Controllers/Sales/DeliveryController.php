<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Notifications\SalesAssignToDelivery;

class DeliveryController extends Controller
{
    public function deliveries()
    {
        $deliveries = Delivery::withCount(['orders' => function ($query) {
            $query->where('status', 'استلام السائق');
        }])->get(['id', 'name', 'phone']);
        foreach ($deliveries as $delivery) {
            $delivery->status = $delivery->orders_count > 2 ? 'غير متاح' : 'متاح';
        }
        return response()->json($deliveries, 200);
    }

    public function show($id)
    {
        $delivery = Delivery::findOrFail($id)->load(['orders:delivery_date,order_type' => function ($query) {
            $query->where('status', 'استلام السائق');
        }]);
        $deliveryOrders = $delivery->orders::where('status', 'تم التوصيل')->count();
        return response()->json([
            'count' => $deliveryOrders,
            'delivery' => $delivery
        ], 200);
    }

    public function assignToDelivery(Request $request, $id)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);
        $order = Order::findOrFail($validatedData['order_id']);
        if($order->status == 'جاري الاستلام') {
            $order->update(['delivery_id' => $id, 'status'=> 'تم التجهيز']);
            $delivery = Delivery::findOrFail($id);
            $delivery->notify(new SalesAssignToDelivery($order));
            return response()->json(['message' => 'تم تعيين الطلب للسائق'], 200);
        }
        return response()->json(['message' => 'لا يمكن تعيين الطلب للسائق'], 400);
    }
}
