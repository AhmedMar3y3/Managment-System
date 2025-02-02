<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ManagerOrderResource;
use App\Models\Order;
use App\Models\Chef;

class ReturnOrdersController extends Controller
{
    //________________________________________all orders_ return_______________________________________________________________

    public function returnRequests()
    {
        $order = Order::where('status', 'مرتجع')
            ->where('manager_id', auth('manager')->user()->id)
            ->get(['id', 'customer_name', 'order_type','status','delivery_date','image']);
    
        if (!$order) {
            return response()->json([
                'message' => 'لا يوجد معلومات ',
            ], 404);
        }
    
        return response()->json([
            'orders' => $order,
        ], 200); 
    }

//________________________________________________________________________________________________________

}




