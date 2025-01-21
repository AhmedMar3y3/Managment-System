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
    $Allchefs= Chef::where('branch_id',$manager->branch_id)
    ->get(['first_name', 'phone', 'email','image','id']);
    $ordersDone= Chef::with('orders')->where('status', 'تم التجهيز" ')->count();
    return response()->json([
        'Allchefs'=>$Allchefs,
        'ordersDone'=>$ordersDone,
    ]);
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

}
