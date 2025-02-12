<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Sale;
use App\Http\Controllers\Controller;

class SalesController extends Controller
{
    public function pendingSales()
    {
        $sales = Sale::where('status', 'pending')->get(['id', 'first_name', 'last_name', 'image']);
        return response()->json(['key' => 'sales', 'data' => $sales], 200);
    }

    public function acceptSale($id)
    {
        $sale =  Sale::find($id);
        if ($sale->status == 'pending') {
            $sale->update(['status' => 'approved']);
            return response()->json('Sale accepted', 200);
        }
        return response()->json('Action cannot be performed', 200);
    }
    public function rejectSale($id)
    {
        $sale = Sale::find($id);
        if ($sale->status == 'pending') {
            $sale->update(['status' => 'declined']);
            return response()->json('Sale rejected', 200);
        }
        return response()->json('Action cannot be performed', 403);
    }

    public function index()
    {
        $sales = Sale::where('status', 'approved')->get(['id', 'first_name', 'last_name', 'phone','email']);
        $sales = $sales->map(function ($sale) {
            $sale->orders_count = $sale->orders()->count();
            return $sale;
        });
        return response()->json($sales, 200);
    }

    public function show($id)
    {
        $sale = Sale::find($id)->load('orders');
        if ($sale) {
            return response()->json($sale, 200);
        }
        return response()->json('Action cannot be performed', 404);
    }
    public function deleteSale($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $sale->delete();
            return response()->json('Sale deleted successfully', 200);
        }
        return response()->json('Action cannot be performed', 404);
    }
}
