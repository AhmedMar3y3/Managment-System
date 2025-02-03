<?php

namespace App\Http\Controllers\Manager;
//____________________________________________________________________________________________________________

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chef;
use App\Models\Manager;
use App\Models\Order;
//____________________________________________________________________________________________________________
class ChefController extends Controller
{
//____________________________________________________________________________________________________________
public function chefs()
{
    $manager=auth('manager')->user();
    $chefs= Chef::where('branch_id',$manager->branch_id)
    ->with('specialization')
    ->withCount(['orders' => function ($query) {
        $query->where('status', 'قيد التنفيذ');
    }])->get(['id', 'first_name', 'last_name', 'phone', 'image','specialization_id']);

    $chefs->each(function ($chef) {
        $chef->canTakeOrder = $chef->orders_count > 5 ? 'غير متاح' : 'متاح';
        $chef->orderCount = $chef->orders_count;
        unset($chef->orders_count);
    });
    
    return response()->json([
        'employees' => $chefs->map(function ($chef) {
            return [
                'id' => $chef->id,
                'first_name' => $chef->first_name,
                'last_name' => $chef->last_name,
                'phone' => $chef->phone,
                'image' => $chef->image,
                'specialization'=> $chef->specialization->name,
                'canTakeOrder' => $chef->canTakeOrder,
                'orderCount' => $chef->orderCount,
            ];
        }),
    ], 200);
}

//____________________________________________________________________________________________________________

public function showChef(string $id)
{
    $Chef = Chef::select('first_name', 'phone', 'image','email','id')->findOrFail($id);
    $ordersDone = Chef::with('orders')->where('status', 'تم التجهيز')->count();
    $Receiving = Chef::with('orders')->where('status', 'تم التجهيز')->count();
    if ($Receiving <= 3) {
        $canTakeOrder = 'متاح';
    } else {
        $canTakeOrder = 'غير متاح';
    }
    return response()->json([
        
        'Chef' => $Chef,
        'orders'=>$ordersDone,
    ],200);
}
//____________________________________________________________________________________________________________
public function CurrentRequests()
{
    $manager = auth('manager')->user(); 
    if (!$manager) {
        return response()->json(['error' => 'لايوجد مدير'], 401);
    }
    $orders = Order::whereIn('status', ['تم القبول', 'قيد التنفيذ'])
    ->where('manager_id', $manager->id)
    ->get();
    return response()->json(['orders'=>$orders],200);
}
//____________________________________________________________________________________________________________

public function CurrentRequestsDelivery()
{
    $delivery = auth('manager')->user();
    if (!$delivery) {
        return response()->json(['error' => 'لايوجد مدير'], 401);
    }
    $orders = Order::whereIn('status', ['تم القبول', 'قيد التنفيذ'])
    ->where('delivery_id', $delivery->id)
    ->get();
    return response()->json(['orders'=>$orders],200);
}

}
