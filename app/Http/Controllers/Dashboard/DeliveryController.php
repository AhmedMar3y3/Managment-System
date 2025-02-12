<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::where('status', 'approved')->with('branch')->get(['id', 'first_name', 'last_name', 'phone', 'branch_id']);
        $deliveries = $deliveries->map(function ($delivery) {
            $delivery->orders_count = $delivery->orders()->count();
            return $delivery;
        });
        return response()->json($deliveries, 200);
    }

    public function show($id)
    {
        $delivery = Delivery::find($id)->load('orders', 'branch');
        if ($delivery) {
            return response()->json($delivery, 200);
        }
        return response()->json('Action cannot be performed', 404);
    }

    public function delete($id)
    {
        $delivery = Delivery::find($id);
        if ($delivery) {
            $delivery->delete();
            return response()->json('Delivery deleted successfully', 200);
        }
        return response()->json('Action cannot be performed', 404);
    }
}
