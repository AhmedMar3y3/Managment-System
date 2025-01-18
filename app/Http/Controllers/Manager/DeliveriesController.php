<?php
//__________________________________________________________________________________________

namespace App\Http\Controllers\Manager;
//__________________________________________________________________________________________

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\Delivery;
//__________________________________________________________________________________________

class DeliveriesController extends Controller
{
//__________________________________________________________________________________________
public function AllDeliveries()
{
    $deliveryInformation=Delivery::select('first_name', 'phone', 'image')->get();
    $deliveryStatus=Delivery::where('status','مقبول')->get()->count();
    return response()->json(['message'=> $deliveryInformation,
    'orders'=>$deliveryStatus,
]);
}
//__________________________________________________________________________________________

public function showDelivery(string $id)
{
    $delivery = Delivery::select('first_name', 'phone', 'image','')->findOrFail($id);
    $Allorder = Delivery::with('orders')->count();
    $ordersDone = Delivery::with('orders')->where('status', 'تم التوصيل')->count();
    $Receiving = Delivery::with('orders')->where('status', 'جاري الاستلام"')->count();
    $deliveryDates = Delivery::with('orders')->first();
    
    if ($Receiving <= 1) {
        $canTakeOrder = 'متاح';
    } else {
        $canTakeOrder = 'غير متاح';
    }

    return response()->json([
        'delivery' => $delivery,
        'Allorder' => $Allorder,
        'ordersDone' => $ordersDone,
        'averageDeliveryDate' => $deliveryDates->delivery_date,
        'canTakeOrder'=>$canTakeOrder
    ]);
}

//__________________________________________________________________________________________



}
