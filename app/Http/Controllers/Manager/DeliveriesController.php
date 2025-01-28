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
    $deliveryInformation=Delivery::select('first_name', 'phone','image','id')->get();
    $deliveryStatus=Delivery::where('status','مقبول')->get()->count();
    return response()->json(['message'=> $deliveryInformation,
    'orders'=>$deliveryStatus,
    
]);
}
//__________________________________________________________________________________________
public function showDelivery(string $id)
{
    $delivery = Delivery::select('first_name', 'phone', 'image','id')->findOrFail($id);
    $ordersDone = Order::where('status', 'تم التوصيل')->count();
    $Receiving = Order::where('status', 'استلام السائق')->count();
    $deliveryDates = Delivery::with('orders')->first();
    
    if ($Receiving <= 1) {
        $canTakeOrder = 'متاح';
    } else {
        $canTakeOrder = 'غير متاح';
    }

    return response()->json([
        'delivery' => $delivery,
        'ordersDone' => $ordersDone,
        'averageDeliveryDate' => $deliveryDates->delivery_date,
        'canTakeOrder'=>$canTakeOrder
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
        if (in_array($order->status, ['تم الرفض', 'مرتجع'])) {
        
            $delivery = Delivery::find($validatedData['delivery_id']);
            $delivery->notify(new OrderRejectedNotification($order));
                return response()->json(['message' => 'تم إرسال الطلب إلى موظف التوصيل بنجاح'], 200);
        }
        return response()->json(['message' => 'خطأ'], 403);

        } 
// ______________________________________________________________________________________________
}


