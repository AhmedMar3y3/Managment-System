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
        $Chef = Chef::with(['specialization', 'orders' => function ($query) {
            $query->where('status', 'قيد التنفيذ')->select('order_type', 'order_details', 'delivery_date', 'chef_id');
        }])->find($id, ['first_name', 'last_name', 'phone', 'image', 'email', 'id', 'bio', 'specialization_id']);

        return response()->json([
            'Chef' => $Chef,
        ], 200);
    }
  
}
