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
//___________________________________________home_page_stats_______________________________________________________________________
public function stats()
{

    $preparedCount = Order::where('manager_id', auth('manager')->user()->id)
        ->where('status', "تم التجهيز")
        ->count();

    $rejectedCount = Order::where('manager_id', auth('manager')->user()->id)
        ->where('status','رفض السائق')
        ->count();

    $deliveredCount = Order::where('manager_id', auth('manager')->user()->id)
        ->where('status', "تم التوصيل")
        ->count();

    $returnedCount = Order::where('manager_id', auth('manager')->user()->id)
        ->where('status', "مرتجع")
        ->count();


        $reciveCount = Order::where('manager_id', auth('manager')->user()->id)
        ->where('status', 'استلام السائق')
        ->count();


$Count = Order::where('manager_id', auth('manager')->user()->id)
    ->where('status', "تم التوصيل")
    ->count();
    
$totalOrders = Order::count();
$Percentage = ($totalOrders > 0) ? ($Count / $totalOrders) * 100 : 0;



return response()->json([
    'prepared' => $preparedCount,
    'rejected' => $rejectedCount,
    'delivered' => $deliveredCount,
    'returned' => $returnedCount,
    'recive' =>  $reciveCount,
    'percentage' => $Percentage . "%", 
    ], 200);
} 

//___________________________________________________________________________________________________________________
    public function inProgressOrders()
    {
        $orders = Order::where('manager_id', auth('manager')->user()->id)
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
