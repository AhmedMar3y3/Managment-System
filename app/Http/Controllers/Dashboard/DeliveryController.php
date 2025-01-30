<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::where('status', 'مقبول')->get(['id', 'first_name', 'last_name']);
        $deliveries = $deliveries->map(function ($delivery) {
            $delivery->orders_count = $delivery->orders()->count();
            return $delivery;
        });
        return response()->json($deliveries, 200);
    }

    public function show($id)
    {
        $delivery = Delivery::find($id);
        if ($delivery) {
            return response()->json($delivery, 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 404);
    }
    public function delete($id)
    {
        $delivery = Delivery::find($id);
        if ($delivery) {
            $delivery->delete();
            return response()->json('تم حذف الشيف بنجاح', 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 404);
    }
}
