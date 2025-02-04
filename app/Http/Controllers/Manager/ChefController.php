<?php

namespace App\Http\Controllers\Manager;

use App\Models\Chef;
use App\Http\Controllers\Controller;

class ChefController extends Controller
{

    // index chefs
    public function chefs()
    {
        $manager = Auth('manager')->user();
        $chefs = Chef::where('branch_id', $manager->branch_id)
            ->with('specialization')
            ->withCount(['orders' => function ($query) {
                $query->where('status', 'قيد التنفيذ');
            }])->get(['id', 'first_name', 'last_name', 'phone', 'image', 'specialization_id']);

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
                    'specialization' => $chef->specialization->name,
                    'canTakeOrder' => $chef->canTakeOrder,
                    'orderCount' => $chef->orderCount,
                ];
            }),
        ], 200);
    }


    // show chef details
    public function showChef(string $id)
    {
        $Chef = Chef::withCount(['orders as completed_orders_count' => function ($query) {
            $query->where('status', 'تم التجهيز');
        }, 'orders as in_progress_orders_count' => function ($query) {
            $query->where('status', 'قيد التنفيذ');
        }])->find($id);

        $Chef->canTakeOrder = $Chef->in_progress_orders_count > 5 ? 'غير متاح' : 'متاح';

        return response()->json([
            'chef' => [
                'id' => $Chef->id,
                'first_name' => $Chef->first_name,
                'last_name' => $Chef->last_name,
                'phone' => $Chef->phone,
                'image' => $Chef->image,
                'email' => $Chef->email,
                'specialization' => $Chef->specialization->name,
                'bio' => $Chef->bio,
                'completed_orders_count' => $Chef->completed_orders_count,
                'canTakeOrder' => $Chef->canTakeOrder,
                'orders' => $Chef->orders->where('status', 'قيد التنفيذ')->values()->map(function ($order) {
                    return [
                        'order_type' => $order->order_type,
                        'order_details' => $order->order_details,
                        'delivery_date' => $order->delivery_date,
                        'delivery_id' => $order->delivery_id,
                    ];
                }),
            ],
        ]);
    }
}
