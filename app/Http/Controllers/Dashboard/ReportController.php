<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;

class ReportController extends Controller
{

    public function deliverySummary(Request $request)
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'date' => 'required|date_format:Y-m-d'
        ]);

        $results = Order::where([
                'delivery_id' => request('delivery_id'),
                'delivery_date' => request('date'),
                'payment_method' => 'cash',
                'status' => 'delivered'
            ])
            ->select([
                DB::raw('SUM(cake_price + flower_price + delivery_price) AS total'),
                DB::raw('SUM(cake_price) AS cakes'),
                DB::raw('SUM(flower_price) AS flowers'),
                DB::raw('SUM(total_price - deposit) AS net'),
                DB::raw('COUNT(*) AS orders_count')
            ])
            ->first();

            $isCompleted = Order::where('delivery_id', request('delivery_id'))
                ->where('delivery_date', request('date'))->first();
        return response()->json([
            'date' => request('date'),
            'delivery_id' => request('delivery_id'),
            'is completed'=> $isCompleted->is_completed,
            'total' => $results->total ?? 0,
            'cakes' => $results->cakes ?? 0,
            'flowers' => $results->flowers ?? 0,
            'net_amount' => $results->net ?? 0,
            'orders_count' => $results->orders_count ?? 0
        ]);
    }

    public function markCompleted(Request $request)
    {
        $request->validate([
           'delivery_id' => 'required|exists:deliveries,id',
            'date' => 'required|date_format:Y-m-d'
        ]);

        DB::transaction(function() {
            Order::where([
                'delivery_id' => request('delivery_id'), 
                'delivery_date' => request('date')
            ])->update([
                'is_completed' => true
            ]);
            
            return response()->json([
                'message' => 'Daily delivery marked as completed',
                'completed_at' => now()->toDateTimeString()
            ]);
        });
    }

    // DeliveryController.php
public function deliveries()
{
    return response()->json(
        Delivery::where(['status' => 'approved'])
            ->get(['id', DB::raw("CONCAT(first_name,' ',last_name) AS name")])
    );
}

}
