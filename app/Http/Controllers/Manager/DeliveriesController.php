<?php

namespace App\Http\Controllers\Manager;

use App\Models\Delivery;
use App\Http\Controllers\Controller;

class DeliveriesController extends Controller
{
    public function AllDeliveries()
    {
        $manager = Auth('manager')->user();
        $deliveries = Delivery::where('branch_id', $manager->branch_id)->withCount(['orders' => function ($query) {
            $query->where('status', 'تم التوصيل');
        }])->get(['id', 'first_name', 'last_name', 'phone', 'image']);

        $deliveries->each(function ($delivery) {
            $delivery->canTakeOrder = $delivery->orders_count > 5 ? 'غير متاح' : 'متاح';
            $delivery->orderCount = $delivery->orders_count;
            unset($delivery->orders_count);
        });

        return response()->json([
            'employees' => $deliveries->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'first_name' => $delivery->first_name,
                    'last_name' => $delivery->last_name,
                    'phone' => $delivery->phone,
                    'image' => $delivery->image,
                    'specialization' => null,
                    'canTakeOrder' => $delivery->canTakeOrder,
                    'orderCount' => $delivery->orderCount,
                ];
            }),
        ], 200);
    }

    public function showDelivery(string $id)
    {
        $delivery = Delivery::with(['orders' => function ($query) {
            $query->where('status', "استلام السائق")->select('order_type', 'order_details', 'delivery_date', 'delivery_id');
        }])->withCount(['orders as delivered_orders_count' => function ($query) {
            $query->where('status', 'تم التوصيل');
        }, 'orders as in_progress_orders_count' => function ($query) {
            $query->where('status', 'استلام السائق');
        }])->find($id, ['first_name', 'last_name', 'phone', 'image', 'email', 'id']);

        $delivery->canTakeOrder = $delivery->in_progress_orders_count > 5 ? 'غير متاح' : 'متاح';

        return response()->json([
            'delivery' => [
                'id' => $delivery->id,
                'first_name' => $delivery->first_name,
                'last_name' => $delivery->last_name,
                'phone' => $delivery->phone,
                'image' => $delivery->image,
                'email' => $delivery->email,
                'delivered_orders_count' => $delivery->delivered_orders_count,
                'canTakeOrder' => $delivery->canTakeOrder,
            ],
        ]);
    }

}
