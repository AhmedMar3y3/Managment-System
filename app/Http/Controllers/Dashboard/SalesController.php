<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class SalesController extends Controller
{
    public function pendingSales()
    {
        $sales = Sale::where('status', 'قيد الانتظار')->get(['id', 'first_name', 'last_name']);
        return response()->json($sales, 200);
    }

    public function acceptSale($id)
    {
        $sale =  Sale::find($id);
        if ($sale->status == 'قيد الانتظار') {
            $sale->update(['status' => 'مقبول']);
            return response()->json('تم قبول السيلز', 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 200);
    }
    public function rejectSale($id)
    {
        $sale = Sale::find($id);
        if ($sale->status == 'قيد الانتظار') {
            $sale->update(['status' => 'مرفوض']);
            return response()->json('تم رفض السيلز', 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 403);
    }

    public function index()
    {
        $sales = Sale::where('status', 'مقبول')->get(['id', 'first_name', 'last_name']);
        $sales = $sales->map(function ($sale) {
            $sale->orders_count = $sale->orders()->count();
            return $sale;
        });
        return response()->json($sales, 200);
    }

    public function show($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            return response()->json($sale, 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 404);
    }
    public function deleteSale($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $sale->delete();
            return response()->json('تم حذف السيلز بنجاح', 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 404);
    }
}
