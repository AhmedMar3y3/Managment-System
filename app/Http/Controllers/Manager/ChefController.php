<?php

namespace App\Http\Controllers\Manager;
//____________________________________________________________________________________________________________

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chef;
use App\Models\Manager;
//____________________________________________________________________________________________________________
class ChefController extends Controller
{
//____________________________________________________________________________________________________________
public function AllchefsInformation()
{
    $Allchefs= Chef::select('first_name', 'phone', 'email','image')->first();
    $ordersDone= Chef::with('orders')->where('status', 'تم التجهيز" ')->count();
    return response()->json([
        'Allchefs'=>$Allchefs,
        'ordersDone'=>$ordersDone,
    ]);
}
//____________________________________________________________________________________________________________
public function chefDetail(string $id)
{
    $Chef = Chef::select('first_name', 'phone', 'image','email')->findOrFail($id);
    $ordersDone = Chef::with('orders')->where('status', 'تم التجهيز')->count();
    $Receiving = Chef::with('orders')->where('status', 'تم التجهيز')->count();
    if ($Receiving <= 3) {
        $canTakeOrder = 'متاح';
    } else {
        $canTakeOrder = 'غير متاح';
    }
    return response()->json([
        'Chef' => $Chef,
        'ordersDone' => $ordersDone,
        'canTakeOrder'=>$canTakeOrder
    ]);
}


//____________________________________________________________________________________________________________

}
