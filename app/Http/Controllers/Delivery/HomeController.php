<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function search(Request $request)
    {
        $deliveryId = Auth('delivery')->id();
        $searchName = $request->input('name');
        $searchPhone = $request->input('phone');

        $orders = Order::where('delivery_id', $deliveryId)
            ->when($searchName || $searchPhone, function ($query) use ($searchName, $searchPhone) {
                $query->where(function ($subQuery) use ($searchName, $searchPhone) {
                    $subQuery->when($searchName, function ($q) use ($searchName) {
                        $q->orWhere('customer_name', 'LIKE', "%{$searchName}%");
                    })
                        ->when($searchPhone, function ($q) use ($searchPhone) {
                            $q->orWhere('customer_phone', 'LIKE', "%{$searchPhone}%");
                        });
                });
            })
            ->with('images')
            ->get();

        return response()->json([
            'message' => 'Search results retrieved successfully',
            'orders' => $orders
        ]);
    }

    public function branchAddress()
    {
        $branchAddress = Branch::where('id', Auth('delivery')->user()->branch_id)->get('address');
        return response()->json($branchAddress, 200);
    }
}
