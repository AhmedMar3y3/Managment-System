<?php
//__________________________________________________________________________________________

namespace App\Http\Controllers\Manager;
//__________________________________________________________________________________________

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\Delivery;
use App\Models\Order;
use App\Notifications\OrderRejectedNotification;
//__________________________________________________________________________________________

class DeliveriesController extends Controller
{
    //__________________________________________________________________________________________
    public function AllDeliveries()
    {
        $manager = Auth('manager')->user();
        $deliveries = Delivery::where('branch_id', $manager->branch_id)->withCount(['orders' => function ($query) {
            $query->where('status', 'استلام السائق');
        }])->get(['id', 'first_name', 'last_name', 'phone', 'image']);

        $deliveries->each(function ($delivery) {
            $delivery->canTakeOrder = $delivery->orders_count > 5 ? 'غير متاح' : 'متاح';
            $delivery->orderCount = $delivery->orders_count;
            unset($delivery->orders_count);
        });

        return response()->json([
            'employees' => $deliveries->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'first_name' => $delivery->first_name,
                    'last_name' => $delivery->last_name,
                    'phone' => $delivery->phone,
                    'image' => $delivery->image,
                    'specialization' => null,
                    'canTakeOrder' => $delivery->canTakeOrder,
                    'orderCount' => $delivery->orderCount,
                ];
            }),
        ], 200);
    }
    //__________________________________________________________________________________________
    public function showDelivery(string $id)
    {
        $delivery = Delivery::select('first_name', 'phone', 'image', 'id')->findOrFail($id);
        $ordersDone = Order::where('status', 'تم التوصيل')->count();
        $Receiving = Order::where('status', 'استلام السائق')->count();
        $deliveryDates = Delivery::with('orders')->first();

        if ($Receiving <= 2) {
            $canTakeOrder = 'متاح';
        } else {
            $canTakeOrder = 'غير متاح';
        }

        return response()->json([
            'delivery' => $delivery,
            'ordersDone' => $ordersDone,
            'averageDeliveryDate' => $deliveryDates->delivery_date,
            'canTakeOrder' => $canTakeOrder
        ]);
    }
    //__________________________________________________________________________________________
    public function assignOrderToDelivery(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'delivery_id' => 'required|integer|exists:deliveries,id',
        ]);
        $order = Order::find($validatedData['order_id']);
        $order->update([
            'delivery_id' => $validatedData['delivery_id'],
        ]);
        if (in_array($order->status, ['تم التجهيز', 'رفض السائق', 'مرتجع'])) {

            $delivery = Delivery::find($validatedData['delivery_id']);
            $delivery->notify(new OrderRejectedNotification($order));
            return response()->json(['message' => 'تم إرسال الطلب إلى موظف التوصيل بنجاح'], 200);
        }
        return response()->json(['message' => 'خطأ'], 403);
    }
    // ______________________________________________________________________________________________
}
