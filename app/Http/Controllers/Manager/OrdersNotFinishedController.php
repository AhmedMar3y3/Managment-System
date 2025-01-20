<?php

namespace App\Http\Controllers\Manager;
//___________________________________________________________________________________________________________________

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//___________________________________________________________________________________________________________________
use Auth;
use App\Models\Manager;
use App\Models\Order;
use App\Models\Chef;
use App\Models\OrderImage;
//___________________________________________________________________________________________________________________
class OrdersNotFinishedController extends Controller
{
//___________________________________________________________________________________________________________________
public function stats(){
$accept=Order::where('status','تم القبول')->count();
$dlivered=Order::where('status','تم التوصيل')->count();
$totaldlivered=$accept-$dlivered;
return response()->json(['delvered_orders'=>$totaldlivered]);
}
//___________________________________________________________________________________________________________________
    public function inProgressOrders()
    {
        $manager = auth('manager')->user();
    
        $orders = Order::where('manager_id', $manager->id)
            ->where('status',  'قيد التنفيذ')
            ->orderBy('delivery_date', 'desc')
            ->get(['id', 'customer_name', 'order_details', 'order_type']);

        return response()->json([

            'orders'=>$orders,
            'rate'=>50
        ],200);
    }
//___________________________________________________________________________________________________________________
public function NewOrders(){
    $orders = Order::where('status', 'جاري الاستلام')
    ->orderBy('created_at', 'desc')
    ->get(['id','order_type','delivery_date','order_details']);
    return response()->json([
        'orders' =>$orders
    ],200);
}
//___________________________________________________________________________________________________________________
public function ShowNewOrder(string $id){
    
    $orders = Order::findOrFail($id);
    return response()->json([
        'order_number' =>$orders->id,
        'customer_address'=>$orders->customer_address,
        'customer_phone'=>$orders->customer_phone,
        'order_details'=>$orders->order_details,
        'customer_name'=>$orders->customer_name,
        'order_date' =>$orders->delivery_date,
        'order_type' =>$orders->order_type,
        'notes'=>$orders->notes,
        'price'=>$orders->price,
        
    ],200);
}
//___________________________________________________________________________________________________________________



}
