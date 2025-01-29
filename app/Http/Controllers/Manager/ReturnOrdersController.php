<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ManagerOrderResource;
use App\Models\Order;
use App\Models\Chef;

class ReturnOrdersController extends Controller
{
    public function returnRequests()
    {
        $order = Order::where('status', "مرتجع")
            ->where('manager_id', auth('manager')->user()->id)
            ->get(['id', 'order_type', 'customer_name', 'delivery_date', 'status']);
    
        if (!$order) {
            return response()->json([
                'message' => 'لا يوجد معلومات ',
            ], 404);
        }
    
        return response()->json([
            'order' => $order,
        ], 200);
    }

//________________________________________________________________________________________________________

}




